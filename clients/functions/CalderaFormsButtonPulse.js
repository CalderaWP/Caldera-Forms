//-- Do NOT use ES6 syntax here, since this is used in editor, which does not use babel --//
/**
 * Makes arbitrary button pulse
 *
 * @since 1.5.0.9
 *
 * @param $btn The button as a jQuery object
 * @constructor
 */
export default function CalderaFormsButtonPulse( $btn ){

    var pulseEffect,
        pulseLoop,
        stopped = false;

    /**
     * Animates the pulse effect
     *
     * @since 1.5.0.9
     */
    pulseEffect = function() {
        $btn.animate({
            opacity: 0.25
        }, 500 , function() {
            $btn.animate({
                opacity: 1
            }, 500 );
        });

    };

    /**
     * Starts the pulse effect loop
     *
     * @since 1.5.0.9
     */
    this.startPulse = function() {
        if( false ===  stopped ){
            pulseLoop = setInterval( function(){
                pulseEffect();
            }, 1000 );
        }



    };

    /**
     * Ends the pulse effect loop
     *
     * @since 1.5.0.9
     */
    this.stopPulse = function() {
        stopped = true;
        clearInterval(pulseLoop);

    };

};