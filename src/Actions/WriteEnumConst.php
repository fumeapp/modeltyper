<?php

namespace FumeApp\ModelTyper\Actions;

use ReflectionClass;

class WriteEnumConst
{
    /**
     * Write the enum const.
     *
     * @param  string  $indent
     * @param  ReflectionClass  $reflection
     * @return string
     */
    public function __invoke(string $indent, ReflectionClass $reflection): string
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
            $entry .= "{$indent}const {$reflection->getShortName()} = {\n";

            $cases->each(function ($case) use ($indent, &$entry, $comments) {
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

                $entry .= "{$indent}  {$name}: {$value},\n";
            });

            $entry .= "{$indent}} as const;\n\n";
            $entry .= "{$indent}export type {$reflection->getShortName()} = typeof {$reflection->getShortName()}[keyof typeof {$reflection->getShortName()}]\n\n";
        }

        return $entry;
    }
}
