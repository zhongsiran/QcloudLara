<template>
  <div class="col">
    <button v-if="sp_item.finish_status != 'finished'" type="button" class="btn btn-primary">设为完成</button>
    <button v-else type="button" class="btn btn-danger">取消完成</button>
  </div>
</template>

<script>
export default {
  // props: ['sp_item', 'corp'],
  data: function () {
    return {
      sp_item: window.Backend.sp_item,
    }
  },
  mounted() {
    console.log('Done Undone Btn Ok')
  },
};
</script>