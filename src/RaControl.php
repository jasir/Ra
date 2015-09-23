<?php
namespace Ra;

use Nette\Application\UI\Control;
use Nette\DI\PhpReflection;
use Nette\Utils\Validators;

/**
 * @author Jaroslav Povolný (jasir) <jaroslav.povolny@gmail.com>
 */
class RaControl extends Control
{

    /** @var Props */
    protected $props;


    /**
     * @param Props $props
     */
    final public function __construct(Props $props = null)
    {
        if ($props === null) {
            $props = new Props();
        }
        $this->setProps($props);
        parent::__construct();
        $this->initialize();
    }


    protected function initialize()
    {
    }


    /**
     * @return Props
     */
    final public function getProps()
    {
        return $this->props;
    }


    /**
     * @param Props $props
     * @return $this
     */
    final public function setProps($propsOrCallable)
    {
        if (is_callable($propsOrCallable)) {
            $props = call_user_func($propsOrCallable, $this->getProps(), $this);
        } else {
            $props = $propsOrCallable;
        }
        $this->props = $props;
        $this->autowireProps();
        return $this;
    }


    /**
     * @param $filePath
     * @return \Nette\Application\UI\ITemplate
     */
    public function createFileTemplate($filePath = null)
    {
        $template = $this->createTemplate();
        if ($filePath) {
            $template->setFile($filePath);
        }
        return $template;
    }


    public function createStringTemplate($content = null)
    {

    }


    final public function render()
    {
        //own render function, can abort rendering

        if ($this->props->hasProp('onRender')) {
            $continue = $this->props->onRender($this, $this->props);
            if ($continue !== true) {
                return;
            }
        }

        //or render template

        $template = null;

        if ($this->props->hasProp('onTemplateCreate')) {
            $template = $this->props->onTemplate($this, $this->props);
        }

        if (!$template) {
            $template = $this->createTemplate();
        }

        if ($this->props->hasProp('templatePath')) {
            $template->setFile($this->props->templatePath);
        }

        //configured template can be configured

        if ($this->props->hasProp('onTemplateConfigure')) {
            $this->props->onTemplate($template);
        }

        if ($template->getFile() === null) {
            $template->setFile(__DIR__ . '/templates/no-template.latte');
        }

        $template->props = $this->props;

        $template->render();
    }


    protected function validateProps()
    {
        //todo:
    }


    /**
     * Creates Component
     *
     * priority: props, $this->componentFactory(), createComponent<Name> methods
     * @param  string      component name
     * @return IComponent  the created component (optionally)
     */
    final protected function createComponent($name)
    {
        if ($this->props->hasProp('onComponentCreate')) {
            $component = $this->props->onComponentCreate($this, $name);
            if ($component) {
                return $component;
            }
        }

        $component = $this->onComponentCreate($this, $name);

        if ($component) {
            return $component;
        }

        $component = parent::createComponent($name);
        if ($component) {
            return $component;
        }

        //stub control

        $props = new Props();
        $props->templatePath = __DIR__ . '/templates/no-control.latte';
        /*$props->onRender = function (RaControl $control) {
            if ($control->getParent() instanceOf StubControl) {
                $control->getParent()->render();
                return false;
            }
            return true; //continue rendering
        };*/

        return new StubControl($props);

    }


    protected function onComponentCreate(RaControl $currentControl, $name)
    {
    }


    /** privates */

    private function autowireProps()
    {
        $rc = $this->getReflection();

        foreach ($rc->getProperties() as $property) {

            $annotation = $property->getAnnotation('prop');
            if ($annotation) {

                $type = (string)$property->getAnnotation('var');
                $propName = isset($annotation['name']) ? $annotation['name'] : $property->name;

                //todo: implement @prop(type=specifictype)

                $value = $this->props->get($propName);

                if (in_array($type, ['array', 'int', 'numeric', 'string'])) {
                    if (!Validators::is($value, $type)) {
                        throw new PropValidationException(
                            "Prop {$propName} is not of required type {$type}but " . $this->getObjectType($value)
                        );
                    }
                } else {
                    $type = PhpReflection::expandClassName($type, $rc);
                    if (!$value instanceOf $type) {
                        throw new PropValidationException(
                            "Prop {$propName} is not of required type {$type}, but " . $this->getObjectType($value)
                        );
                    }
                }

                $this->{$property->name} = $value;
            }

        }
    }


    private function getObjectType($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }


}
