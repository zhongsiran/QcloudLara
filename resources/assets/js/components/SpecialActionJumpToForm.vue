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
        <form class="form-inline-sm" >
            <div class="form-row align-items-center">
              <div class="col-auto">
                  <label class="sr-only" for="inlineFormInputGroup2">sp-corp-filter</label>
                  <div class="input-group mb-2">
                  <div class="input-group-prepend">
                      <div class="input-group-text">名称</div>
                  </div>
                  <input type="text" class="form-control" id="inlineFormInputGroup2" placeholder="输入企业名称进行搜索" v-model="corpToFilter">
                  </div>
              </div>
              <div class="col-auto">
                  <a href="javascript:;" class="btn btn-info mb-2 text-white" @click="corp_filter(corpToFilter)">搜索</a>
              </div>
            </div>
        </form>
        <table class="table table-striped">
          <th v-if="Object.keys(corpFilterResult).length">搜索结果（在上面输入序号来跳转）</th>
          <th v-if="corpFilterNotFound">没有找到带{{corpToFilter}}的企业名称</th>
          <tr v-for="(value, key) in corpFilterResult" :key="key">
            <th>{{value}}</th>
            <td>{{key}}</td>
          </tr>
        </table>
    </div>
</template>

<script>
export default {
  data: function() {
    return {
      jumpToItem: '',
      max_item: window.Backend.max_item,
      corp_list: window.Backend.corp_list,
      corpToFilter: '',
      corpFilterResult: new Object,
      corpFilterNotFound: false
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
    },
    corp_filter: function(keyword) {
      this.corpFilterResult = new Object
      this.corpFilterNotFound = false
      let corp_list_array = Object.keys(this.corp_list)
      let corpFilterNames = corp_list_array.filter(function(item) {
        return item.match(keyword)
      })
      if (corpFilterNames.length == 0) {
        this.corpFilterNotFound = true
      }
      corpFilterNames.forEach(element => {
        Vue.set(this.corpFilterResult, this.corp_list[element], element)
      });
    }
  },
  computed: {
    placeholder: function() {
      return "最大序號為" + this.max_item;
    }
  },
  watch: {
    corpToFilter: function(val) {
      this.corpFilterNotFound = false
    }
  }
};
</script>