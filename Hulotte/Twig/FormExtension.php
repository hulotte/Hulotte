<?php

namespace Hulotte\Twig;

use \DateTime;

/**
 * Class FormExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class FormExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('field_checkbox', [$this, 'fieldCheckbox'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ]),
            new \Twig_SimpleFunction('field_input', [$this, 'fieldInput'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ]),
            new \Twig_SimpleFunction('field_radio', [$this, 'fieldRadio'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ]),
            new \Twig_SimpleFunction('field_select', [$this, 'fieldSelect'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ]),
            new \Twig_SimpleFunction('field_textarea', [$this, 'fieldTextarea'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ]),
        ];
    }

    /**
     * Create checkboxes fields
     * @param array $context
     * @param string $key
     * @param null|string $label
     * @param null|array $checkedValue
     * @param array $options
     * @return string
     */
    public function fieldCheckbox(
        array $context,
        string $key,
        ?string $label = null,
        ?array $checkedValue,
        array $options
    ): string {
        $error = $this->getErrorHtml($context, $key);
        $attributes = $this->getAttributes($options, $key, $error);

        $class = '';

        if ($attributes['class'] !== '') {
            $class = ' class="' . $attributes['class'] . '"';
        }

        $html = '<div' . $class . '>';

        if ($label) {
            $html .= $label;
        }

        $html .= $this->getHtmlCheckboxes($key, $checkedValue, $options['checkboxes']);
        $html .= $error;
        $html .= '</div>';

        return $html;
    }

    /**
     * Create input field
     * @param array $context The context was define by Twig
     * @param string $key
     * @param null|string $label
     * @param null|mixed $value
     * @param array $options
     * @return string
     */
    public function fieldInput(
        array $context,
        string $key,
        ?string $label = null,
        $value = null,
        array $options = []
    ): string {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $attributes = $this->getAttributes($options, $key, $error, $value);

        $html = '<div>';

        if ($label) {
            $html .= '<label for="' . $key . '">' . $label . '</label>';
        }

        $html .= '<input type="' . $type . '" ' . $this->getHtmlFromArray($attributes) . '>';
        $html .= $error;
        $html .= '</div>';

        return $html;
    }

    /**
     * Create radios fields
     * @param array $context
     * @param string $key
     * @param null|string $label
     * @param null|string $checkedValue
     * @param array $options
     * @return string
     */
    public function fieldRadio(
        array $context,
        string $key,
        ?string $label = null,
        ?string $checkedValue,
        array $options
    ): string {
        $error = $this->getErrorHtml($context, $key);
        $attributes = $this->getAttributes($options, $key, $error);

        $class = '';

        if ($attributes['class'] !== '') {
            $class = ' class="' . $attributes['class'] . '"';
        }

        $html = '<div' . $class . '>';

        if ($label) {
            $html .= $label;
        }

        $html .= $this->getHtmlRadios($key, $checkedValue, $options['radios']);
        $html .= $error;
        $html .= '</div>';

        return $html;
    }

    /**
     * Create select field
     * @param array $context
     * @param string $key
     * @param null|string $label
     * @param null|string $selectedValue
     * @param array $options
     * @return string
     */
    public function fieldSelect(
        array $context,
        string $key,
        ?string $label = null,
        ?string $selectedValue = null,
        array $options
    ): string {
        $error = $this->getErrorHtml($context, $key);
        $class = null;
        $attributes = $this->getAttributes($options, $key, $error);

        $html = '<div>';

        if ($label) {
            $html .= '<label for="' . $key . '">' . $label . '</label>';
        }

        $html .= '<select ' . $this->getHtmlFromArray($attributes) . '>';
        $html .= $this->getHtmlOptions($selectedValue, $options['options']);
        $html .= '</select>';
        $html .= $error;
        $html .= '</div>';

        return $html;
    }

    /**
     * Create textarea field
     * @param array $context The context was define by Twig
     * @param string $key
     * @param null|string $label
     * @param null|mixed $value
     * @param array $options
     * @return string
     */
    public function fieldTextarea(
        array $context,
        string $key,
        ?string $label = null,
        $value = null,
        array $options = []
    ): string {
        $error = $this->getErrorHtml($context, $key);
        $attributes = $this->getAttributes($options, $key, $error);

        $html = '<div>';

        if ($label) {
            $html .= '<label for="' . $key . '">' . $label . '</label>';
        }

        $html .= '<textarea ' . $this->getHtmlFromArray($attributes) . '>' . $value . '</textarea>';
        $html .= $error;
        $html .= '</div>';

        return $html;
    }

    /**
     * Create array with attributes
     * @param array $options
     * @param string $key
     * @param null|string $error
     * @param null|string $value
     * @return array
     */
    private function getAttributes(array $options, string $key, ?string $error, ?string $value = null): array
    {
        $attributes = [
            'class' => trim($options['class'] ?? ''),
            'name' => $key,
            'id' => $key
        ];

        if ($error) {
            if ($attributes['class'] !== '') {
                $attributes['class'] .= ' ';
            }

            $attributes['class'] .= 'alert';
        }

        if ($value) {
            $attributes['value'] = $value;
        }

        return $attributes;
    }

    /**
     * Create html tags with errors
     * @param array $context
     * @param string $key
     * @return null|string
     */
    private function getErrorHtml(array $context, string $key): ?string
    {
        $error = $context['errors'][$key] ?? false;

        if ($error) {
            return "<small>$error</small>";
        }

        return null;
    }

    /**
     * Create checkboxes fields
     * @param string $name
     * @param null|array $checkedValue
     * @param array $checkboxesTag
     * @return string
     */
    private function getHtmlCheckboxes(string $name, ?array $checkedValue, array $checkboxesTag): string
    {
        return array_reduce(
            array_keys($checkboxesTag),
            function (string $html, string $key) use ($checkboxesTag, $checkedValue, $name) {
                if ($checkedValue) {
                    $checked = in_array($key, $checkedValue);
                } else {
                    $checked = false;
                }

                $params = ['id' => $key, 'name' => $name . '[]', 'value' => $key, 'checked' => $checked];

                $html .= '<div>';
                $html .= '<input type="checkbox" ' . $this->getHtmlFromArray($params) . '>';
                $html .= '<label for="' . $key . '">' . $checkboxesTag[$key] . '</label>';
                $html .= '</div>';

                return $html;
            },
            ''
        );
    }

    /**
     * Format html form attributes
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes): string
    {
        $htmlParts = [];

        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false && $value !== '') {
                $htmlParts[] = "$key=\"$value\"";
            }
        }

        return implode(' ', $htmlParts);
    }

    /**
     * Create options fields
     * @param null|string $selectedValue
     * @param array $optionsTag
     * @return string
     */
    private function getHtmlOptions(?string $selectedValue, array $optionsTag): string
    {
        return array_reduce(
            array_keys($optionsTag),
            function (string $html, string $key) use ($optionsTag, $selectedValue) {
                $params = ['value' => $key, 'selected' => $key === $selectedValue];

                return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $optionsTag[$key] . '</option>';
            },
            ''
        );
    }

    /**
     * Create radios fields
     * @param string $name
     * @param null|string $checkedValue
     * @param array $radiosTag
     * @return string
     */
    private function getHtmlRadios(string $name, ?string $checkedValue, array $radiosTag): string
    {
        return array_reduce(
            array_keys($radiosTag),
            function (string $html, string $key) use ($radiosTag, $checkedValue, $name) {
                $params = ['id' => $key, 'name' => $name, 'value' => $key, 'checked' => $key === $checkedValue];

                $html .= '<div>';
                $html .= '<input type="radio" ' . $this->getHtmlFromArray($params) . '>';
                $html .= '<label for="' . $key . '">' . $radiosTag[$key] . '</label>';
                $html .= '</div>';

                return $html;
            },
            ''
        );
    }
}
