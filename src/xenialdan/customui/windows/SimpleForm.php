<?php

namespace xenialdan\customui\windows;

use pocketmine\form\FormValidationException;
use pocketmine\Player;
use xenialdan\customui\elements\Button;
use xenialdan\customui\elements\UIElement;

class SimpleForm implements CustomUI
{
    use CallableTrait;

    protected $title = '';
    protected $content = '';
    protected $buttons = [];
    private $id;

    public function __construct($title, $content = '')
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function addButton(Button $button)
    {
        $this->buttons[] = $button;
    }

    final public function jsonSerialize()
    {
        $data = [
            'type' => 'form',
            'title' => $this->title,
            'content' => $this->content,
            'buttons' => []
        ];
        foreach ($this->buttons as $button) {
            $data['buttons'][] = $button;
        }
        return $data;
    }

    final public function getTitle()
    {
        return $this->title;
    }

    public function getContent(): array
    {
        return [$this->content, $this->buttons];
    }

    public function setID(int $id)
    {
        $this->id = $id;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getElement(int $index): Button
    {
        return $this->buttons[$index];
    }

    public function setElement(UIElement $element, int $index)
    {
        if (!$element instanceof Button) return;
        $this->buttons[$index] = $element;
    }

    public function handleResponse(Player $player, $data): void
    {
        if (!is_numeric($data)) {
            $this->close($player);
            return;
        }
        $return = "";
        if (isset($this->buttons[$data])) {
            if (!is_null($value = $this->buttons[$data]->handle($data, $player))) $return = $value;
        } else {
            error_log(__CLASS__ . '::' . __METHOD__ . " Button with index {$data} doesn't exists.");
        }

        $callable = $this->getCallable();
        if ($callable !== null) {
            $callable($player, $return);
        }
    }
}