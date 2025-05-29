<?php

namespace Koneko\VuexyAdmin\Application\Modules;

class ModulePackageRegistrarService
{
    public function registerFromMetadata(ModuleMetadataDTO $metadata, array $extra = []): ModulePackage
    {
        return ModulePackage::updateOrCreate([
            'name' => $metadata->name,
        ], array_merge([
            'display_name'   => $metadata->displayName,
            'description'    => $metadata->description,
            'keywords'       => $metadata->keywords,
            'author_name'    => $metadata->authorName,
            'author_email'   => $metadata->authorEmail,
            'composer'       => $metadata->toArray(),
            'repository_type'=> 'public',
            'active'         => true,
        ], $extra));
    }
}