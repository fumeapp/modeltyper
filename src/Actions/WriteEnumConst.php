<?php

namespace FumeApp\ModelTyper\Actions;

use ReflectionClass;

class WriteEnumConst
{
    /**
     * Write the enum const to the output.
     *
     * @return array{type: string, name: string}|string
     */
    public function __invoke(ReflectionClass $reflection, string $indent = '', bool $jsonOutput = false, bool $useEnums = false): array|string
    {
        $entry = '';

        $docBlock = $reflection->getDocComment();

        $comments = [];
        $docBlock = $reflection->getDocComment();
        if ($docBlock) {
            $pattern = "#(@property+\s*[a-zA-Z0-9, ()_].*)#";
            preg_match_all($pattern, $docBlock, $matches, PREG_PATTERN_ORDER);
            $comments = array_map(fn ($match) => trim(str_replace('@property', '', $match)), $matches[0]);
        }

        $cases = collect($reflection->getConstants());

        if ($cases->isNotEmpty()) {
            if ($useEnums) {
                $entry .= "{$indent}export const enum {$reflection->getShortName()} {\n";
            } else {
                $entry .= "{$indent}const {$reflection->getShortName()} = {\n";
            }

            $cases->each(function ($case) use ($indent, &$entry, $comments, $useEnums) {
                $name = $case->name;
                $value = is_string($case->value) ? "'{$case->value}'" : $case->value;

                // write comments if they exist
                if (! empty($comments)) {
                    foreach ($comments as $comment) {
                        if (str_starts_with($comment, $name)) {
                            $comment = str_replace($name, '', $comment);
                            $comment = preg_replace('/[^a-zA-Z0-9\s]/', '', $comment);
                            $comment = trim($comment);
                            $entry .= "{$indent}  /** $comment */\n";
                            break;
                        }
                    }
                }

                if ($useEnums) {
                    $entry .= "{$indent}  {$name} = {$value},\n";
                } else {
                    $entry .= "{$indent}  {$name}: {$value},\n";
                }
            });

            if ($useEnums) {
                $entry .= "{$indent}}\n\n";
                $entry .= "{$indent}export type {$reflection->getShortName()}Enum = `\${{$reflection->getShortName()}}`\n\n";
            } else {
                $entry .= "{$indent}} as const;\n\n";
                $entry .= "{$indent}export type {$reflection->getShortName()} = typeof {$reflection->getShortName()}[keyof typeof {$reflection->getShortName()}]\n\n";
            }

        }

        if ($jsonOutput) {
            return [
                'name' => $reflection->getShortName(),
                'type' => $entry,
            ];
        }

        return $entry;
    }
}
