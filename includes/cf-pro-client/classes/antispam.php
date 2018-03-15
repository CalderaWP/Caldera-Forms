<?php


namespace calderawp\calderaforms\pro;
use \calderawp\calderaforms\pro\api\message;

class antispam
{
    /**
     * @var message
     */
    protected $message;

    /**
     * @var array
     */
    protected $form;

    /**
     * antispam constructor.
     *
     * @param message $message
     * @param array $form
     */
    public function __construct(message $message, array $form ) {
        $this->message = $message;
        $this->form = $form;

    }

    /**
     * Should anti-spam be checked for?
     *
     * @since 1.6.0
     *
     * @return bool
     */
    public function should_check(){
        return isset( $this->form[ 'antispam' ] ) && ! empty( $this->form[ 'antispam' ][ 'enable']);
    }

    /**
     * Get anti-spam args
     *
     * @since 1.6.0
     *
     * @return array
     */
    public function get_args(){
        $args =  array(
            'url' => caldera_forms_get_current_url(),
            'site_url' => get_home_url(),
            'ip' => caldera_forms_get_ip(),
            'user_agent' => $_SERVER[ 'HTTP_USER_AGENT' ],
            'referrer' =>  $_SERVER[ 'HTTP_REFERER' ],
            'type' =>  'contact-form',
            'email' =>  $this->get_sender_email(),
            'name' => $this->get_sender_name(),
            'lang' => get_locale(),
            'content' =>  $this->message->get_content(),
        );
        return $args;
    }

    /**
     * Get sender name
     *
     * @since 1.6.0
     *
     * @return string
     */
    protected function get_sender_name(){
        return ! empty(  $this->form['antispam'] ) && isset( $this->form['antispam'][ 'sender_name' ]) ? \Caldera_Forms::do_magic_tags($this->form['antispam'][ 'sender_name' ]) : '';
    }

    /**
     * Get sender email
     *
     * @since 1.6.0
     *
     * @return string
     */
    protected function get_sender_email(){
        return ! empty( $this->form['antispam'] ) && isset( $this->form['antispam'][ 'sender_email' ]) ? \Caldera_Forms::do_magic_tags($this->form['antispam'][ 'sender_email' ],null, $this->form) : $this->message->to[ 'email' ];
    }


}