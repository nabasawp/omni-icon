<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\FrameworkBundle\Command;

use OmniIconDeps\Symfony\Component\Console\Attribute\AsCommand;
use OmniIconDeps\Symfony\Component\Workflow\Command\WorkflowDumpCommand as BaseWorkflowDumpCommand;
trigger_deprecation('symfony/framework-bundle', '7.4', 'The "%s" class is deprecated, use "%s" instead.', WorkflowDumpCommand::class, BaseWorkflowDumpCommand::class);
/**
 * @deprecated since Symfony 7.4, use {@see BaseWorkflowDumpCommand} instead.
 */
#[AsCommand(name: 'workflow:dump', description: 'Dump a workflow')]
class WorkflowDumpCommand extends BaseWorkflowDumpCommand
{
}
