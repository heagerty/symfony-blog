<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container0m653vM\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container0m653vM/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container0m653vM.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container0m653vM\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \Container0m653vM\srcApp_KernelDevDebugContainer([
    'container.build_hash' => '0m653vM',
    'container.build_id' => 'fc971424',
    'container.build_time' => 1559836134,
], __DIR__.\DIRECTORY_SEPARATOR.'Container0m653vM');
