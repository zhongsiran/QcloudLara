<template>
  <form class="shadow rounded">
    <div class="form-row justify-content-center">
      <label for=""><b>进度</b></label>
    </div>
    <div class="form-row">
      <div class="col">
        <a v-if="sp_item.finish_status != '已经完成'" class="btn btn-block btn-primary" href="javascript:;" @click="setDone">完成核查</a>
        <a v-else class="btn btn-block btn-secondary" href="javascript:;" @click="setUndone">已完成核查，点击取消完成状态</a>

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
  },
  methods: {
    setDone: function() {
      let id = this.sp_item.id;
      let finish_status = "已经完成";
      axios
        .post(
          "https://www.shilingaic.cn/index.php/api/special_action_set_finish_status/" +
            id,
          {
            finish_status: finish_status
          }
        )
        .then(res => {
          this.sp_item.finish_status = res.data;
        });
    },
    setUndone: function() {
      let id = this.sp_item.id;
      let finish_status = null;
      axios
        .post(
          "https://www.shilingaic.cn/index.php/api/special_action_set_finish_status/" +
            id,
          {
            finish_status: finish_status
          }
        )
        .then(res => {
          this.sp_item.finish_status = res.data;
        });
    }
  }
};
</script>