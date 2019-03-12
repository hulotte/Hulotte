<?php

namespace Hulotte\Module;

/**
 * Interface WidgetInterface
 *
 * @package Hulotte\Module
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
interface WidgetInterface
{
    /**
     * Render the widget
     * @return null|string
     */
    public function render(): ?string;
    
    /**
     * Render the menu
     * @return null|string
     */
    public function renderMenu(): ?string;
}
