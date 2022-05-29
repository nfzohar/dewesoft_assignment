<template>
    <button class="align-content-center bg-white" v-on:click="fetchEventList()">Refresh</button>   
    <div v-if="eventList == null">
        <li class="list-group-item">
            <h4 style="text-align: center;">There are no events to show.</h4> 
        </li> 
    </div>  
    <div v-else>
        <div v-for="event in eventList">
            <li class="list-group-item list-group-item-action d-flex gap-3 py-3 text-wrap" aria-current="true" v-on:click="showEventDetails(event.id)">
                <div class="d-flex gap-2 w-100 justify-content-between">
                    <div> 
                        <h5 class="mb-0">{{ event.event_title }}</h5>            
                        <p v-bind:id="event.id" class="mb-0 opacity-75 mt-3" v-bind:hidden="true">
                            <hr>
                            {{ event.event_description }}
                        </p> 
                    </div>
                    <small class="text-nowrap">{{ event.event_start_time }}</small>
                </div>
            </li>
        </div>
    </div>          
</template>

<script>
    export default {

        data() {
            return {
                eventList: [],
                event: {
                    "item_id": '',
                    "event_title": '',
                    "event_start_time": '',
                    "event_description": ''
                }
            }
        },

        mounted() {
            this.fetchEventList();
        },

        methods: {
            
            fetchEventList() {
                fetch('api/events')
                .then(res => res.json())
                .then( data => this.eventList = data.events)
                .catch(err => console.log(err.message)); 
            },

            showEventDetails(id) {
                if(document.getElementById(id).hidden == true){
                    document.getElementById(id).hidden = false;
                }
                else if(document.getElementById(id).hidden == false){
                    document.getElementById(id).hidden = true;
                }
            },
        }        
    }
</script>

<style>
    p {
        width: 18rem;
    }
</style>