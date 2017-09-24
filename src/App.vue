<template>
  <div id="app">
    <page-header></page-header>
    <div class="container">
        <div class="row">
          <div class="col-md-6">
              <Torrent></Torrent>
          </div>
          <div class="col-md-6">
              <Hash2Torrent></Hash2Torrent>
          </div>
        </div>
    </div>
  </div>
</template>

<script>
import PageHeader from './components/PageHeader.vue';
import Torrent from './components/Torrent.vue';
import Hash2Torrent from './components/Hash2Torrent.vue';

export default {
  name: 'app',
  components: { PageHeader, Torrent, Hash2Torrent }
}
</script>
