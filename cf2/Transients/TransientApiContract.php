<?php


namespace calderawp\calderaforms\cf2\Transients;


interface TransientApiContract
{

    /**
     * Get stored transient
     *
     * @since 1.8.0
     *
     * @param string $id Transient ID
     * @param mixed $data Data
     * @param null|int $expires Optional. Expiration time. Default is nul, which becomes 1 hour
     *
     * @return bool
     */
    public function setTransient($id, $data, $expires = null);

    /**
     * Delete transient
     *
     * @since 1.8.0
     *
     * @param string $id Transient ID
     *
     * @return bool
     */
    public function deleteTransient( $id );

    /**
     * Get a transient
     *
     * @since 1.8.0
     *
     * @param string $id Transient ID
     *
     * @return mixed
     */
    public  function getTransient( $id );

}