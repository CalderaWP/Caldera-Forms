<?php
namespace Smtpapi;

class Header
{
    const NAME = 'x-smtpapi';

    public $to = array();
    public $sub = array();
    public $unique_args = array();
    public $category = array();
    public $section = array();
    public $filters = array();
    public $send_at = null;
    public $send_each_at = array();
    public $asm_group_id = null;
    public $ipPool = null;

    /**
     * @param $email
     * @param string|null $name
     * @return $this
     */
    public function addTo($email, $name = null)
    {
        $this->to[] = $name ? sprintf('%s <%s>', $name, $email) : $email;

        return $this;
    }

    /**
     * @param array $emails
     * @return $this
     */
    public function setTos(array $emails)
    {
        $this->to = $emails;
        return $this;
    }

    /**
     * @param int $send_at
     * @return $this
     */
    public function setSendAt($send_at)
    {
        $this->send_at = $send_at;
        $this->send_each_at = array();

        return $this;
    }

    /**
     * @param array $send_each_at
     * @return $this
     */
    public function setSendEachAt(array $send_each_at)
    {
        $this->send_each_at = $send_each_at;
        $this->send_at = null;

        return $this;
    }

    /**
     * @param int $send_at
     * @return $this
     */
    public function addSendEachAt($send_at)
    {
        $this->send_at = null;
        $this->send_each_at[] = $send_at;

        return $this;
    }

    /**
     * @param string $from_value
     * @param array $to_values
     * @return $this
     */
    public function addSubstitution($from_value, array $to_values)
    {
        $this->sub[$from_value] = $to_values;

        return $this;
    }

    /**
     * @param array $key_value_pairs
     * @return $this
     */
    public function setSubstitutions(array $key_value_pairs)
    {
        $this->sub = $key_value_pairs;

        return $this;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function addUniqueArg($key, $value)
    {
        $this->unique_args[$key] = $value;

        return $this;
    }

    /**
     * @param array $key_value_pairs
     * @return $this
     */
    public function setUniqueArgs(array $key_value_pairs)
    {
        $this->unique_args = $key_value_pairs;

        return $this;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function addCategory($category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * @param array $categories
     * @return $this
     */
    public function setCategories(array $categories)
    {
        $this->category = $categories;

        return $this;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = array($category);
        return $this;
    }

    /**
     * @param string $from_value
     * @param string $to_value
     * @return $this
     */
    public function addSection($from_value, $to_value)
    {
        $this->section[$from_value] = $to_value;

        return $this;
    }

    /**
     * @param array $key_value_pairs
     * @return $this
     */
    public function setSections(array $key_value_pairs)
    {
        $this->section = $key_value_pairs;

        return $this;
    }

    /**
     * @param string $filter_name
     * @param string $parameter_name
     * @param mixed $parameter_value
     * @return $this
     */
    public function addFilter($filter_name, $parameter_name, $parameter_value)
    {
        $this->filters[$filter_name]['settings'][$parameter_name] = $parameter_value;

        return $this;
    }

    /**
     * @param array $filter_setting
     * @return $this
     */
    public function setFilters(array $filter_setting)
    {
        $this->filters = $filter_setting;

        return $this;
    }

    /**
     * @return array filters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $group_id
     * @return $this
     */
    public function setASMGroupID($group_id)
    {
        $this->asm_group_id = $group_id;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setIpPool($name)
    {
        $this->ipPool = $name;

        return $this;
    }

    /**
     * @return array
     */
    private function toArray()
    {
        $data = array();

        if ($this->to) {
            $data['to'] = $this->to;
        }
        if ($this->sub) {
            $data['sub'] = $this->sub;
        }
        if ($this->unique_args) {
            $data['unique_args'] = $this->unique_args;
        }
        if ($this->category) {
            $data['category'] = $this->category;
        }
        if ($this->section) {
            $data['section'] = $this->section;
        }
        if ($this->filters) {
            $data['filters'] = $this->filters;
        }
        if ($this->send_at) {
            $data['send_at'] = $this->send_at;
        }
        if ($this->send_each_at) {
            $data['send_each_at'] = $this->send_each_at;
        }
        if ($this->asm_group_id) {
            $data['asm_group_id'] = $this->asm_group_id;
        }
        if ($this->ipPool) {
            $data['ip_pool'] = $this->ipPool;
        }

        return $data;
    }

    /**
     * @param null $options
     * @return string
     */
    public function jsonString($options = null)
    {
        if ($options === null) {
            $options = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
        }

        if (count($this->toArray()) <= 0) {
            return '{}';
        }

        // unescape 5.3 PHP's escaping of forward slashes
        return str_replace(
            '\\/', '/',
            json_encode($this->toArray(), $options)
        );
    }
}
