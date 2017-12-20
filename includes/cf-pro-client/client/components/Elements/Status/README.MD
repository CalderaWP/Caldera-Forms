Reusable status/ alert indicator.

## Props:
* message - string - required
    The message to show
* success - string - default false
    If true, shown as alert, if false, shown as success.
* show - boolean - default false
    If true, message shows, if false, it does not.
    
## Use as component 

```
    <template>
        <div>
            <status
                :message="'Hi Roy'"
                :success="true"
                :show="status.show"
            >
            </status>
            <button @click="show">Show"</button>
            <button @click="hide">Hide"</button>
        <div>
    </template>
    <script>
       import Status from '?'
       export default {
            components: {
                status: Status
            },
            data(){
                return {
                    showStatus : false
                }
            },
            methods: {
                show(){
                    this.showStatus = true;
                },
                hide(){
                    this.showStatus = false;
                }
            }
       }
   </script>
```
         
## Update main alert in CF Pro
Child component
```
    <template>
        <div>
            <button @click="show">Show"</button>
            <button @click="showFor">Show For 2 Seconds"</button>
            <button @click="hide">Hide"</button>
        <div>
    </template>
    <script>
       export default {
            methods: {
                show(){
                    this.$store.dispatch( 'updateMainAlert', { message: 'Hi Roy', show: true, success: true };
                },
                hide(){
                    this.$store.dispatch( 'closeMainAlert' );
                },
                showFor(){
                    this.$store.dispatch( 'updateMainAlert', { fade: 2000, message: 'Set fade in milliseconds', show: true, success: true };
                }
            }
       }
   </script>
```