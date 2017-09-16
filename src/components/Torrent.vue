<template>
<div class="container">
    <div class="row">
      <div class="col-md-8">
        <input type="file" id="file" v-on:change="selectFile" name="files" accept="application/x-bittorrent" style="display:none;">
        <button type="button" v-on:click="startSelectFiles" class="btn btn-primary">选择种子文件</button>
      </div>
    </div>
    <div v-if="torrentInfo" class="row">
        <div class="col-md-8">
        <h3>磁力链接</h3>
        <div class="input-group">
          <input type="text" id="magnet" class="form-control" v-model="torrentInfo.magnet" />
          <span class="input-group-btn">
            <button type="button" id="copy-magnet" data-clipboard-target="#magnet" class="btn btn-default copy-button" title="复制到剪贴板">复制</button>
          </span>
        </div>
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
            <tr>
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
    </div>
    <div v-else class="row">
        <div class="col-md-8">
            <p class="help-msg">选择种子文件，这里将展示磁力链接和种子详细信息</p>
        </div>
    </div>
</div>
</template>
<script>
import axios from 'axios';
export default {
  data(){
    return {
        torrentInfo: null
    }
  },
  mounted:function(){
    var clipboard = new Clipboard('#copy-magnet');
    clipboard.on('success', function(e) {
        $.notify({
        	message: '复制成功'
        },{
        	type: 'success',
            delay: 1000,
            allow_dismiss: false,
            animate: {
        		enter: 'animated fadeInDown',
        		exit: 'animated fadeOutUp'
        	},
            z_index: 9999,
            placement: {
    			align: 'center'
    		}
        });
    });
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
  },
  methods: {
    startSelectFiles: function(){
        window.document.getElementById('file').click();
    },
    selectFile: function(evt){
        var file = evt.target.files[0];
        var formData = new FormData();
        formData.append("file", file);
        axios.post('https://torrent-vvv123.rhcloud.com/', formData)
        .then((response) => {
          console.log(response.data);
          this.torrentInfo = response.data;
        }).catch(err => {
            $.notify({
            	message: '出错啦，请稍后重试'
            },{
            	type: 'danger',
                delay: 1000,
                allow_dismiss: false,
                animate: {
            		enter: 'animated fadeInDown',
            		exit: 'animated fadeOutUp'
            	},
                z_index: 9999,
                placement: {
        			align: 'center'
        		}
            });
        })
    }
  }

}
</script>
<style>
.container{
  margin-top:80px;
}
.grey{
  color: #999;
}
.help-msg{
    margin-top: 20px;
}
.alert-success{
    text-align: center;
}
.container{
    margin-bottom: 30px;
}
</style>
