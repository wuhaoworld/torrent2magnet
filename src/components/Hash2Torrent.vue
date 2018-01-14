<template>
<div>
    <div class="col-md-12">
          <h3>磁力链接转种子</h3>
          <div class="input-group">
            <input type="text" v-model="magnet" id="magnet" name="magnet" class="form-control" placeholder="请输入磁力链接">
            <span class="input-group-btn">
              <button v-if="status!='pending'" class="btn btn-primary" v-on:click="getTorrent" type="button" style="width:72px;">转换</button>
              <button v-else class="btn btn-primary" disabled v-on:click="getTorrent" type="button" style="width:72px;">处理中...</button>
            </span>
          </div>
    </div>
    <div v-if="downloadUrl && status=='done'" class="col-md-12">
        <a :href="downloadUrl" onclick="_hmt.push(['_trackEvent', 'convert', 'download']);" download class="btn btn-default" style="margin-top:20px;">点此下载种子</a>

        <h3>种子详情</h3>
        <table class="table table-bordered">
            <tbody>
            <tr>
              <th style="width:76px;">任务名称</th>
              <td>{{ torrentInfo.name }}</td>
          </tr>
          <tr>
            <th>infohash</th>
            <td>{{ torrentInfo.infohash }}</td>
          </tr>
            <tr v-if="torrentInfo.publisher">
              <th scope="row">发布者</th>
              <td>{{ torrentInfo.publisher }}</td>
            </tr>
            <tr v-if="torrentInfo['created-at']">
              <th scope="row">创建时间</th>
              <td>{{ torrentInfo['created-at'] }}</td>
            </tr>
            <tr>
              <th scope="row">总大小</th>
              <td>{{ torrentInfo.files.total_size | readablizeBytes }}</td>
            </tr>
            <tr v-if="torrentInfo.comment">
              <th scope="row">备注</th>
              <td>{{ torrentInfo.comment }}</td>
            </tr>
          </tbody>
        </table>
        <h3>文件列表 ({{ torrentInfo.files.file_count }} 个文件)</h3>
        <ul v-for="file in torrentInfo.files.files">
            <li>{{ file.name }} <span class="grey">( {{ file.size | readablizeBytes }} )</span></li>
        </ul>
        <h3>announce ({{ torrentInfo['announce-list'].length }}) 个</h3>
        <ul v-for="announce in torrentInfo['announce-list']">
            <li>{{ announce }}</li>
        </ul>

    </div>
    <div v-else-if="downloadUrl=='' && status=='done'" class="col-md-12">
        <p  style="margin-top:10px;">暂找不到该磁力链接对应的种子。</p>
    </div>
    <div v-else class="col-md-12">
    </div>
</div>
</template>
<script>
import axios from 'axios';
import config from '../../config';
export default {
  data(){
    return {
        magnet: '',
        status: 'ready',
        torrentInfo: null,
        downloadUrl: ""
    }
  },
  methods: {
      getTorrent: function(){
          _hmt.push(['_trackEvent', 'convert', 'start']);
          if(this.magnet == ""){
              alert('请输入磁力链接');
              return;
          }
          var infohash = this.magnet.match(/[a-zA-Z0-9]{40}/);
          if(!infohash){
              alert('请输入磁力链接');
              return;
          }else{
              infohash = infohash[0];
          }
          this.status = "pending";
          axios.get(config.apiUrl + 'api/hash2Torrent.php', {
              params: {
                infohash: infohash
              }
          })
          .then((response) => {
              if(response.data.status == "success"){
                  this.torrentInfo = response.data;
                  this.status = 'done';
                  this.downloadUrl = response.data.download_url;
                  console.log(response.data);
                  this.downloadUrl = "http://35.200.148.180/url2bt/api/download.php?infohash=" + response.data.infohash;
                  _hmt.push(['_trackEvent', 'convert', 'success']);
              }else{
                  this.status = 'done';
                  this.downloadUrl = "";
                  _hmt.push(['_trackEvent', 'convert', 'no torrent']);
              }
            }).catch(err => {
                this.status = 'done';
                _hmt.push(['_trackEvent', 'convert', 'error']);
                console.log(err);
            });
      }
  },
  filters: {
      readablizeBytes:function(bytes){
        bytes = parseInt(bytes);
        if(bytes==0){
            return '0 MB';
        }
        var s = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
        var e = Math.floor(Math.log(bytes)/Math.log(1024));
        return (bytes/Math.pow(1024, Math.floor(e))).toFixed(2)+" "+s[e];
      }
  }
}
</script>
<style>
h3{
    margin-bottom:16px;
}
</style>
