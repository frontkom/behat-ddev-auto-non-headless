<?php

namespace eiriksm\BehatAutoDdevNonHeadless;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DisableHeadlessExtension implements Extension {

  public function load(ContainerBuilder $container, array $config) {
  }

  public function process(ContainerBuilder $container) {
    if (getenv('IS_DDEV_PROJECT') !== 'true') {
      return;
    }
    $def = $container->getDefinition('mink');
    $calls = $def->getMethodCalls();
    foreach ($calls as $delta => $call) {
      if ($call[0] !== 'registerSession') {
        continue;
      }
      if (empty($call[1][0])) {
        continue;
      }
      if ($call[1][0] !== 'selenium2') {
        continue;
      }
      if (empty($call[1][1])) {
        continue;
      }
      if (!$call[1][1] instanceof Definition) {
        continue;
      }
      // Now grab that dynamic definition.
      $session_definition = $call[1][1];
      $arguments = $session_definition->getArguments();
      if (empty($arguments[0])) {
        continue;
      }
      if (!$arguments[0] instanceof Definition) {
        continue;
      }
      $driver = $arguments[0];
      // Change the arguments of this one.
      $driver_args = $driver->getArguments();
      if (empty($driver_args[1])) {
        continue;
      }
      if (empty($driver_args[1]["chrome"]["switches"][0])) {
        continue;
      }
      // Unset whatever starts with "--headless".
      foreach ($driver_args[1]["chrome"]["switches"] as $key => $switch) {
        if (strpos($switch, '--headless') === 0) {
          unset($driver_args[1]["chrome"]["switches"][$key]);
        }
      }
      $driver_args[1]["chrome"]["switches"] = array_values($driver_args[1]["chrome"]["switches"]);
      // Now set it back.
      $driver->setArguments($driver_args);
      $arguments[0] = $driver;
      $session_definition->setArguments($arguments);
      $calls[$delta][1][1] = $session_definition;
      $def->setMethodCalls($calls);
    }
  }

  public function getConfigKey() {
  }

  public function initialize(ExtensionManager $extensionManager) {
  }

  public function configure(ArrayNodeDefinition $builder) {
  }

}