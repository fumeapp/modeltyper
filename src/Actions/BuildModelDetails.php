<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Symfony\Component\Finder\SplFileInfo;

class BuildModelDetails
{
    use ClassBaseName;
    use ModelRefClass;

    /**
     * Build the model details.
     *
     * @param  SplFileInfo  $modelFile
     * @return array
     */
    public function __invoke(SplFileInfo $modelFile): array
    {
        $modelDetails = app(RunModelShowCommand::class)($modelFile->getBasename('.php'));

        $reflectionModel = $this->getRefInterface($modelDetails);
        $laravelModel = $reflectionModel->newInstance();
        $databaseColumns = $laravelModel->getConnection()->getSchemaBuilder()->getColumnListing($laravelModel->getTable());

        $name = $this->getClassName($modelDetails['class']);
        $columns = collect($modelDetails['attributes'])->filter(fn ($att) => in_array($att['name'], $databaseColumns));
        $nonColumns = collect($modelDetails['attributes'])->filter(fn ($att) => ! in_array($att['name'], $databaseColumns));
        $relations = collect($modelDetails['relations']);

        return [
            'reflectionModel' => $reflectionModel,
            'name' => $name,
            'columns' => $columns,
            'nonColumns' => $nonColumns,
            'relations' => $relations,
        ];
    }
}
