<template>
    <div class="container btn-group" role="group">
        <div class="row">
            <corp-location-and-photo-upload-button></corp-location-and-photo-upload-button>
            <special-action-set-done-and-undone-button></special-action-set-done-and-undone-button>
        </div>
    </div>
</template>

<script>
export default {
  data: function () {
    return {
      sp_item: (window.Backend.sp_item ? window.Backend.sp_item : ''),
      corp: (window.Backend.corp ? window.Backend.corp : ''),
    }
  },
  mounted() {
    console.log('Done Undone Btn Ok')
  },
};
</script>