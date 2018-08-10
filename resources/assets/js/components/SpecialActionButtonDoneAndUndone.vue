<template>
  <form class="shadow rounded">
    <div class="form-row justify-content-center">
      <label for=""><b>进度</b></label>
    </div>
    <div class="form-row">
      <div class="col">
        <a v-if="sp_item.finish_status != '已经完成'" class="btn btn-block btn-primary" href="javascript:;">完成核查</a>
        <a v-else class="btn btn-block btn-secondary" href="javascript:;">已完成核查，点击取消完成状态</a>

      </div>
    </div>
  </form>
</template>

<script>
export default {
  data: function() {
    return {
      sp_item: window.Backend.sp_item
    };
  },
  mounted() {
    console.log("Done Undone Btn Ok");
  }
};
</script>