<template>
    <div class="jump-to-form">
        <form class="form-inline-sm" >
            <div class="form-row align-items-center">
            <div class="col-auto">
                <label class="sr-only" for="inlineFormInputGroup">sp-corp-id</label>
                <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">序号</div>
                </div>
                <input type="number" class="form-control" id="inlineFormInputGroup" :placeholder="placeholder" v-model="jumpToItem">
                </div>
            </div>
            <div class="col-auto">
                <a href="javascript:;" class="btn btn-info mb-2 text-white" @click="jump">跳转</a>
            </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
  data: function() {
    return {
      max_item: window.Backend.max_item,
      jumpToItem: ""
    };
  },
  methods: {
    jump: function() {
      if (this.jumpToItem <= this.max_item) {
        let page = Math.floor(this.jumpToItem / 10) + 1;
        let targetUrl =
          "https://" +
          location.host +
          location.pathname +
          "?page=" +
          page +
          "#" +
          this.jumpToItem;
        location.href = targetUrl;
      } else {
          alert('不能超過最大序號(' + this.max_item + '號)')
      }
    }
  },
  computed: {
    placeholder: function() {
      return "最大序號為" + this.max_item;
    }
  }
};
</script>