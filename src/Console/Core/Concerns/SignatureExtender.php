<?php

namespace Dust\Console\Core\Concerns;

trait SignatureExtender
{
    public function addModuleOption(): void
    {
        $this->extendSignature(
            '
            {--module= : Specify a module.}
        '
        );
    }

    public function extendSignature(string $text): void
    {
        $this->signature .= $text;
    }
}
