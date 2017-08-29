<?php

namespace Drupal\entity_submenu_block\Plugin\Block;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\system\Plugin\Block\SystemMenuBlock;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Entity Submenu Block.
 *
 * @Block(
 *   id = "entity_submenu_block",
 *   admin_label = @Translation("Entity Submenu Block"),
 *   category = @Translation("Menus"),
 *   deriver = "Drupal\entity_submenu_block\Plugin\Derivative\EntitySubmenuBlock"
 * )
 */
class EntitySubmenuBlock extends SystemMenuBlock {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * All entity types.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface[]
   */
  protected $entityTypes;

  /**
   * Constructs an EntitySubmenuBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_tree
   *   The menu tree service.
   * @param \Drupal\Core\Menu\MenuActiveTrailInterface $menu_active_trail
   *   The active menu trail service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   The entity display repository.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MenuLinkTreeInterface $menu_tree, MenuActiveTrailInterface $menu_active_trail, EntityTypeManagerInterface $entity_type_manager, EntityDisplayRepositoryInterface $entity_display_repository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $menu_tree, $menu_active_trail);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->entityTypes = $this->entityTypeManager->getDefinitions();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu.link_tree'),
      $container->get('menu.active_trail'),
      $container->get('entity_type.manager'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // We don't need the menu levels options from the parent.
    unset($form['menu_levels']);

    $config = $this->getConfiguration();

    // Display non-entities option.
    $form['display_non_entities'] = [
      '#title' => $this->t('Display non-entities'),
      '#type' => 'checkbox',
      '#default_value' => $this->getDefaultValue($config, 'display_non_entities'),
    ];

    // View modes fieldgroup.
    $form['view_modes'] = [
      '#title' => $this->t('View modes'),
      '#description' => $this->t('View modes to be used when submenu items are displayed as content entities'),
      '#type' => 'fieldgroup',
      '#process' => [[get_class(), 'processFieldSets']],
    ];

    // A select list of view modes for each entity type.
    foreach ($this->getEntityTypes() as $entity_type) {
      $field = 'view_mode_' . $entity_type;
      $view_modes = $this->entityDisplayRepository->getViewModeOptions($entity_type);
      $form['view_modes'][$field] = [
        '#title' => $this->entityTypeManager->getDefinition($entity_type)->getLabel(),
        '#type' => 'select',
        '#options' => $view_modes,
        '#default_value' => $this->getDefaultValue($config, $field, array_keys($view_modes)),
      ];
    }

    return $form;
  }

  /**
   * Form API callback: Processes the elements in field sets.
   *
   * Adjusts the #parents of field sets to save its children at the top level.
   */
  public static function processFieldSets(&$element, FormStateInterface $form_state, &$complete_form) {
    array_pop($element['#parents']);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('display_non_entities', $form_state->getValue('display_non_entities'));
    foreach ($this->getEntityTypes() as $entity_type) {
      $field = 'view_mode_' . $entity_type;
      $value = $form_state->getValue($field);
      $this->setConfigurationValue($field, $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'entity_submenu',
      '#menu_name' => NULL,
      '#menu_items' => [],
    ];

    // Get the menu name.
    $menu_name = $this->getDerivativeId();

    // Return empty menu items array if the active trail is not in this menu.
    if (empty($this->menuActiveTrail->getActiveLink($menu_name))) {
      return $build;
    }

    // The menu name is only set if the active trail is in this menu.
    $build['#menu_name'] = $menu_name;

    $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);

    // Get current level from end of active trail.
    $level = count($parameters->activeTrail);
    $parameters->setMinDepth($level);

    // We only want the current level.
    $parameters->setMaxDepth($level);

    // We only want enabled links.
    $parameters->onlyEnabledLinks();

    $tree = $this->menuTree->load($menu_name, $parameters);
    $manipulators = array(
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $this->menuTree->transform($tree, $manipulators);

    $config = $this->getConfiguration();
    foreach ($tree as $element) {
      $url = $element->link->getUrlObject();
      $routeParams = $url->getRouteParameters();
      reset($routeParams);
      $entity_type = key($routeParams);
      if ($url->isRouted() && in_array($entity_type, $this->getEntityTypes())) {
        $entity = $this->entityTypeManager->getStorage($entity_type)->load($routeParams[$entity_type]);
        $build['#menu_items'][] = $this->entityTypeManager->getViewBuilder($entity_type)->view($entity, $config['view_mode_' . $entity_type]);
      }
      elseif ($config['display_non_entities'] == 1) {
        $build['#menu_items'][] = [
          '#title' => $element->link->getTitle(),
          '#type' => 'link',
          '#url' => $url,
        ];
      }
    }

    return $build;
  }

  /**
   * Returns a default value for a specified field.
   *
   * @param array $config
   *   Array containing the configuration.
   * @param string $field
   *   Name of the field to get a value for.
   * @param array $valid
   *   Optional array containing valid values for the field.
   *
   * @return value
   *   Value for the field.
   */
  protected function getDefaultValue(array $config, $field, array $valid = NULL) {
    $value = NULL;
    if (isset($config[$field]) && !empty($config[$field])) {
      if (is_array($valid)) {
        if (in_array($config[$field], $valid)) {
          $value = $config[$field];
        }
      }
      else {
        $value = $config[$field];
      }
    }

    return $value;
  }

  /**
   * Returns a list of valid entity types.
   *
   * @return array
   *   Valid entity type names.
   */
  protected function getEntityTypes() {
    $entity_types = ['node'];
    foreach ($this->entityTypes as $entity_type => $definition) {
      if ($entity_type != 'node' && $this->isValidEntity($entity_type)) {
        $entity_types[] = $entity_type;
      }
    }

    return $entity_types;
  }

  /**
   * Filters entities based on their view builder handlers.
   *
   * @param string $entity_type
   *   The entity type of the entity that needs to be validated.
   *
   * @return bool
   *   TRUE if the entity has the correct view builder handler, FALSE if the
   *   entity doesn't have the correct view builder handler.
   */
  protected function isValidEntity($entity_type) {
    return $this->entityTypes[$entity_type]->get('field_ui_base_route') && $this->entityTypes[$entity_type]->hasViewBuilderClass();
  }

}
