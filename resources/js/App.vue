<template>
  <ol class="list-group w-auto">
    <EventList/>
  </ol>
</template>

<script>
  import EventList from './components/EventList.vue'

  export default {
    name: 'App',
    components: {
      EventList
    }
  }
</script>

