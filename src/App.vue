<template>
  <div id="app">
    <page-header></page-header>
    <Torrent></Torrent>
  </div>
</template>

<script>
import PageHeader from './components/PageHeader.vue';
import Torrent from './components/Torrent.vue';

export default {
  name: 'app',
  components: { PageHeader, Torrent }
}
</script>
