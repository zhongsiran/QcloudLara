<template>
    <div class="col">
        <button type="button" class="btn btn-primary">上传照片</button>
        <button type="button" class="btn btn-primary">上传定位</button>
    </div>
</template>

<script>
export default {
  // props: ['sp_item', 'corp'],
  data: function () {
    return {
      corp: window.Backend.corp
    }
  },
  mounted() {
    console.log('Loc Pic Btn ok')
  },
};
</script>