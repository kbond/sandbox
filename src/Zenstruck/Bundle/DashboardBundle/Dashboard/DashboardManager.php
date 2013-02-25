<?php

namespace Zenstruck\Bundle\DashboardBundle\Dashboard;

use Knp\Menu\MenuItem;
use Knp\Menu\Silex\RouterAwareFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DashboardManager
{
    protected $config;
    protected $urlGenerator;
    protected $securityContext;

    public function __construct($config, UrlGeneratorInterface $urlGenerator, SecurityContextInterface $securityContext)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->securityContext = $securityContext;
    }

    public function getLayout()
    {
        return $this->config['layout'];
    }

    public function getTitle()
    {
        return $this->config['title'];
    }

    /**
     * @return \Knp\Menu\MenuItem
     */
    public function getFullMenu()
    {
        return $this->getMenu($this->config['menu']);
    }

    /**
     * @param $group
     *
     * @return \Knp\Menu\MenuItem
     */
    public function getGroupedMenu($group)
    {
        $menuConfig = array_filter($this->config['menu'], function($item) use ($group) {
                return $group === $item['group'];
            });

        return $this->getMenu($menuConfig);
    }

    /**
     * @return \Knp\Menu\MenuItem
     */
    public function getUngroupedMenu()
    {
        $menuConfig = array_filter($this->config['menu'], function($item) {
                return null === $item['group'];
            });

        return $this->getMenu($menuConfig);
    }

    /**
     * @param array $menuConfig
     *
     * @return \Knp\Menu\MenuItem
     */
    protected function getMenu(array $menuConfig)
    {
        $menu = new MenuItem('root', new RouterAwareFactory($this->urlGenerator));

        foreach ($menuConfig as $sectionName => $section) {
            $nested = true;

            if ($section['nested']) {
                $subMenu = $menu->addChild($sectionName);

                $this->setLabel($subMenu, $sectionName);

                if ($icon = $section['icon']) {
                    $subMenu->setExtra('icon', $icon);
                }
            } else {
                $nested = false;
                $subMenu = $menu;
            }

            foreach ($section['items'] as $itemName => $item) {
                // security check
                if ($item['role'] && $this->securityContext->getToken() && !$this->securityContext->isGranted($item['role'])) {
                    continue;
                } else {
                    $menuItem = $subMenu->addChild($itemName, $item);

                    $this->setLabel($menuItem, $itemName);

                    if (!$nested) {
                        $menuItem->setExtra('flat', true);
                    }

                    if ($icon = $item['icon']) {
                        $menuItem->setExtra('icon', $icon);
                    }
                }
            }

            // remove empty sections
            if ($nested && !count($subMenu->getChildren())) {
                $menu->removeChild($sectionName);
            }
        }

        return $menu;
    }

    protected function setLabel(MenuItem $menu, $text)
    {
        // check for <foo> or <foo:bar> syntax
        if (preg_match('/<(\w+)(:(\w+))?>/', $text, $matches)) {
            // create getter
            $method = 'get'.ucfirst($matches[1]);

            $ret = $this->$method();

            // check for <foo:bar> syntax
            if (isset($matches[3])) {
                $method = $matches[3];

                // if <foo:bar> syntax is used, call bar method
                if (is_object($ret) && method_exists($ret, $method)) {
                    $ret = $ret->$method();
                }
            }

            $text = (string) $ret;
        }

        $menu->setLabel($text);
    }

    protected function getUser()
    {
        return $this->securityContext->getToken()->getUser();
    }
}