<script setup>
    import axios from 'axios';
    import { ref, onUnmounted, watchEffect } from 'vue';
    import videojs from 'video.js';
    import 'video.js/dist/video-js.css';
    import { useToastr } from '../../toastr';
    const toastr = useToastr();

    const videos = ref([]);
    const videoUrl = ref(null);
    const videoPlayer = ref(null);
    let player = null;

    const saveCopy = async () => {
        try {
            axios.get('/api/videos/download', {
                params: { url: videoUrl.value }
            }).then((response) => {
                toastr.success('Video successfully saved!');
            });
        } catch (e) {
            toastr.error('Something went wrong.');
            console.log(e);
        };
    };

    const initializePlayer = () => {
        if (videoUrl.value && videoPlayer.value) {
            if (player) {
                player.src({
                    src: videoUrl.value,
                    type: 'video/mp4'
                });
            } else {
                player = videojs(videoPlayer.value, {
                    autoplay: true,
                    controls: true,
                    sources: [{
                        src: videoUrl.value,
                        type: 'video/mp4'
                    }]
                });
            }
        }
    };

    const getVideos = () => {
        axios.get(`/api/videos`).then((response) => {
            videos.value = response.data;
        });
    };

    watchEffect(() => {
        initializePlayer();
        getVideos();
    });

    onUnmounted(() => {
        if (player) {
            player.dispose();
            player = null;
        }
    });
</script>

<template>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Videos</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link to="/admin/dashboard" class="">Dashboard</router-link>
              </li>
              <li class="breadcrumb-item active">Videos</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="row justify-content-center mt-5">
          <div class="col-md-6">
            <div class="card p-4">
              <h3>Lists</h3>
              <ul class="list-unstyled">
                <li v-for="(vid, index) in videos.data" :key="index">
                    <!-- <video width="200" height="150" controls>
                        <source :src="`/${vid.saved_url}`" type="video/mp4">
                        <source :src="`/${vid.saved_url}`" type="video/ogg">
                        Your browser does not support the video tag.
                    </video> -->
                    <div><a :href="`/${vid.saved_url}`" target="_blank">{{ vid.saved_url }}</a></div>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card p-4">
              <div class="form-group d-flex">
                <input type="text" class="form-control" placeholder="Enter video url" v-model="videoUrl">
              </div>

              <div>
                <video ref="videoPlayer" class="video-js vjs-default-skin"></video>
              </div>
              <div v-if="videoUrl && videoPlayer" class="mt-4">
                <button type="button" class="btn btn-primary btn-block" @click="saveCopy">Save Copy</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <style scoped>
  .video-js {
    width: 100% !important;
    height: 350px !important;
    padding: 30px !important;
  }
  </style>
