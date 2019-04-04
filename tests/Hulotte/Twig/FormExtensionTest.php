<?php

namespace Tests\Hulotte\Twig;

use PHPUnit\Framework\TestCase;
use Hulotte\Twig\FormExtension;

/**
 * Class FormExtensionTest
 *
 * @package Tests\Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Twig\FormExtension
 */
class FormExtensionTest extends TestCase
{
    /**
     * @var FormExtension
     */
    private $formExtension;

    public function setUp(): void
    {
        $this->formExtension = new FormExtension();
    }

    /**
     * @covers ::fieldInput
     */
    public function testTextInput(): void
    {
        $html = $this->formExtension->fieldInput([], 'name', 'Your name');

        $assert = '<div>';
        $assert .= '<label for="name">Your name</label>';
        $assert .= '<input type="text" name="name" id="name">';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldInput
     */
    public function testTextInputWithoutLabel(): void
    {
        $html = $this->formExtension->fieldInput([], 'name');

        $assert = '<div>';
        $assert .= '<input type="text" name="name" id="name">';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldInput
     */
    public function testTextInputWithValue(): void
    {
        $html = $this->formExtension->fieldInput([], 'name', 'Your name', 'Sébastien');

        $assert = '<div>';
        $assert .= '<label for="name">Your name</label>';
        $assert .= '<input type="text" name="name" id="name" value="Sébastien">';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldInput
     */
    public function testTextInputWithError(): void
    {
        $html = $this->formExtension->fieldInput(
            ['errors' => ['name' => 'This field has an error.']],
            'name',
            'Your name',
            'Sébastien'
        );

        $assert = '<div>';
        $assert .= '<label for="name">Your name</label>';
        $assert .= '<input type="text" class="alert" name="name" id="name" value="Sébastien">';
        $assert .= '<small>This field has an error.</small>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldInput
     */
    public function testTextInputWithErrorAndClass(): void
    {
        $html = $this->formExtension->fieldInput(
            ['errors' => ['name' => 'This field has an error.']],
            'name',
            'Your name',
            'Sébastien',
            ['class' => 'testClass']
        );

        $assert = '<div>';
        $assert .= '<label for="name">Your name</label>';
        $assert .= '<input type="text" class="testClass alert" name="name" id="name" value="Sébastien">';
        $assert .= '<small>This field has an error.</small>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldInput
     */
    public function testTextInputWithClass(): void
    {
        $html = $this->formExtension->fieldInput([], 'name', 'Your name', null, ['class' => 'testClass']);

        $assert = '<div>';
        $assert .= '<label for="name">Your name</label>';
        $assert .= '<input type="text" class="testClass" name="name" id="name">';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldInput
     */
    public function testEmailInput(): void
    {
        $html = $this->formExtension->fieldInput([], 'name', 'Your name', null, [
            'type' => 'email'
        ]);

        $assert = '<div>';
        $assert .= '<label for="name">Your name</label>';
        $assert .= '<input type="email" name="name" id="name">';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldTextarea
     */
    public function testTextarea(): void
    {
        $html = $this->formExtension->fieldTextarea([], 'description', 'Description');

        $assert = '<div>';
        $assert .= '<label for="description">Description</label>';
        $assert .= '<textarea name="description" id="description"></textarea>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldTextarea
     */
    public function testTextareaWithoutLabel(): void
    {
        $html = $this->formExtension->fieldTextarea([], 'description');

        $assert = '<div>';
        $assert .= '<textarea name="description" id="description"></textarea>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldTextarea
     */
    public function testTextareaWithValue(): void
    {
        $html = $this->formExtension->fieldTextarea([], 'description', 'Description', 'C\'est un test.');

        $assert = '<div>';
        $assert .= '<label for="description">Description</label>';
        $assert .= '<textarea name="description" id="description">C\'est un test.</textarea>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldTextarea
     */
    public function testTextareaWithError(): void
    {
        $html = $this->formExtension->fieldTextarea(
            ['errors' => ['description' => 'This field has an error.']],
            'description',
            'Description'
        );

        $assert = '<div>';
        $assert .= '<label for="description">Description</label>';
        $assert .= '<textarea class="alert" name="description" id="description"></textarea>';
        $assert .= '<small>This field has an error.</small>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldTextarea
     */
    public function testTextareaWithClass(): void
    {
        $html = $this->formExtension->fieldTextarea([], 'description', 'Description', null, ['class' => 'testClass']);

        $assert = '<div>';
        $assert .= '<label for="description">Description</label>';
        $assert .= '<textarea class="testClass" name="description" id="description"></textarea>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldSelect
     */
    public function testSelect(): void
    {
        $html = $this->formExtension->fieldSelect([], 'categories', 'Catégories', null, [
            'options' => [1 => 'Développement', 2 => 'Communication']
        ]);

        $assert = '<div>';
        $assert .= '<label for="categories">Catégories</label>';
        $assert .= '<select name="categories" id="categories">';
        $assert .= '<option value="1">Développement</option>';
        $assert .= '<option value="2">Communication</option>';
        $assert .= '</select>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldSelect
     */
    public function testSelectWithoutLabel(): void
    {
        $html = $this->formExtension->fieldSelect([], 'categories', null, null, [
            'options' => [1 => 'Développement', 2 => 'Communication']
        ]);

        $assert = '<div>';
        $assert .= '<select name="categories" id="categories">';
        $assert .= '<option value="1">Développement</option>';
        $assert .= '<option value="2">Communication</option>';
        $assert .= '</select>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldSelect
     */
    public function testSelectWithErrors(): void
    {
        $html = $this->formExtension->fieldSelect(
            ['errors' => ['categories' => 'This field has an error.']],
            'categories',
            'Catégories',
            null,
            [
                'options' => [1 => 'Développement', 2 => 'Communication']
            ]
        );

        $assert = '<div>';
        $assert .= '<label for="categories">Catégories</label>';
        $assert .= '<select class="alert" name="categories" id="categories">';
        $assert .= '<option value="1">Développement</option>';
        $assert .= '<option value="2">Communication</option>';
        $assert .= '</select>';
        $assert .= '<small>This field has an error.</small>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldSelect
     */
    public function testSelectWithSelectedValue(): void
    {
        $html = $this->formExtension->fieldSelect([], 'categories', 'Catégories', 1, [
            'options' => [1 => 'Développement', 2 => 'Communication']
        ]);

        $assert = '<div>';
        $assert .= '<label for="categories">Catégories</label>';
        $assert .= '<select name="categories" id="categories">';
        $assert .= '<option value="1" selected>Développement</option>';
        $assert .= '<option value="2">Communication</option>';
        $assert .= '</select>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldRadio
     */
    public function testRadio(): void
    {
        $html = $this->formExtension->fieldRadio([], 'civility', 'Civilité', null, [
            'radios' => ['Mme' => 'Mme', 'M.' => 'M.']
        ]);

        $assert = '<div>';
        $assert .= 'Civilité';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="Mme" name="civility" value="Mme">';
        $assert .= '<label for="Mme">Mme</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="M." name="civility" value="M.">';
        $assert .= '<label for="M.">M.</label>';
        $assert .= '</div>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldRadio
     */
    public function testRadioWithoutTitle(): void
    {
        $html = $this->formExtension->fieldRadio([], 'civility', null, null, [
            'radios' => ['Mme' => 'Mme', 'M.' => 'M.']
        ]);

        $assert = '<div>';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="Mme" name="civility" value="Mme">';
        $assert .= '<label for="Mme">Mme</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="M." name="civility" value="M.">';
        $assert .= '<label for="M.">M.</label>';
        $assert .= '</div>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldRadio
     */
    public function testRadioWithCheckedValue(): void
    {
        $html = $this->formExtension->fieldRadio([], 'civility', 'Civilité', 'Mme', [
            'radios' => ['Mme' => 'Mme', 'M.' => 'M.']
        ]);

        $assert = '<div>';
        $assert .= 'Civilité';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="Mme" name="civility" value="Mme" checked>';
        $assert .= '<label for="Mme">Mme</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="M." name="civility" value="M.">';
        $assert .= '<label for="M.">M.</label>';
        $assert .= '</div>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldRadio
     */
    public function testRadioWithError(): void
    {
        $html = $this->formExtension->fieldRadio(
            ['errors' => ['civility' => 'This field has an error.']],
            'civility',
            'Civilité',
            null,
            [
                'radios' => ['Mme' => 'Mme', 'M.' => 'M.']
            ]
        );

        $assert = '<div class="alert">';
        $assert .= 'Civilité';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="Mme" name="civility" value="Mme">';
        $assert .= '<label for="Mme">Mme</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="radio" id="M." name="civility" value="M.">';
        $assert .= '<label for="M.">M.</label>';
        $assert .= '</div>';
        $assert .= '<small>This field has an error.</small>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldCheckbox
     */
    public function testCheckbox(): void
    {
        $html = $this->formExtension->fieldCheckbox([], 'categories', 'Catégories', null, [
            'checkboxes' => [1 => 'Développement', 2 => 'Communication']
        ]);

        $assert = '<div>';
        $assert .= 'Catégories';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="1" name="categories[]" value="1">';
        $assert .= '<label for="1">Développement</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="2" name="categories[]" value="2">';
        $assert .= '<label for="2">Communication</label>';
        $assert .= '</div>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldCheckbox
     */
    public function testCheckboxWithoutTitle(): void
    {
        $html = $this->formExtension->fieldCheckbox([], 'categories', null, null, [
            'checkboxes' => [1 => 'Développement', 2 => 'Communication']
        ]);

        $assert = '<div>';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="1" name="categories[]" value="1">';
        $assert .= '<label for="1">Développement</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="2" name="categories[]" value="2">';
        $assert .= '<label for="2">Communication</label>';
        $assert .= '</div>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldCheckbox
     */
    public function testCheckboxWithError(): void
    {
        $html = $this->formExtension->fieldCheckbox(
            ['errors' => ['categories' => 'This field has an error.']],
            'categories',
            'Catégories',
            null,
            [
                'checkboxes' => [1 => 'Développement', 2 => 'Communication']
            ]
        );

        $assert = '<div class="alert">';
        $assert .= 'Catégories';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="1" name="categories[]" value="1">';
        $assert .= '<label for="1">Développement</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="2" name="categories[]" value="2">';
        $assert .= '<label for="2">Communication</label>';
        $assert .= '</div>';
        $assert .= '<small>This field has an error.</small>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }

    /**
     * @covers ::fieldCheckbox
     */
    public function testCheckboxWithChecked(): void
    {
        $html = $this->formExtension->fieldCheckbox([], 'categories', 'Catégories', [1, 2], [
            'checkboxes' => [1 => 'Développement', 2 => 'Communication']
        ]);

        $assert = '<div>';
        $assert .= 'Catégories';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="1" name="categories[]" value="1" checked>';
        $assert .= '<label for="1">Développement</label>';
        $assert .= '</div>';
        $assert .= '<div>';
        $assert .= '<input type="checkbox" id="2" name="categories[]" value="2" checked>';
        $assert .= '<label for="2">Communication</label>';
        $assert .= '</div>';
        $assert .= '</div>';

        $this->assertEquals($assert, $html);
    }
}
