<template>
  <form class="shadow rounded">
    <div class="form-row justify-content-center">
      <label for=""><b>显示或隐藏照片</b></label>
    </div>
    <div class="form-row">
      <div class="col">
        <a v-if="hidePhoto" class="btn btn-block btn-primary" href="javascript:;" @click="onShowPhoto">显示照片</a>
        <a v-else class="btn btn-block btn-primary" href="javascript:;" @click="onHidePhoto">隐藏照片</a>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  data: function() {
    return {
      hidePhoto: this.$root.hide_photo
    };
  },
  mounted() {},
  methods: {
    onShowPhoto: function() {
      this.$root.$emit("onShowPhoto");
    },
    onHidePhoto: function() {
      this.$root.$emit("onHidePhoto");
    }
  }
};
</script>