
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('general-confirm-delete-photo', require('./components/GeneralModalConfirmDeletePhotoComponent.vue'));
Vue.component('general-form-layout-corp-detail', require('./components/GeneralFormLayoutCorpDetail.vue'));
Vue.component('general-show-photos', require('./components/GeneralShowPhotos.vue'));
Vue.component('general-show-photos-toggle', require('./components/GeneralShowPhotosToggle.vue'));

Vue.component('special-action-done-and-undone-button', require('./components/SpecialActionButtonDoneAndUndone.vue'));
Vue.component('special-action-form', require('./components/SpecialActionFormTimeAndStatus.vue'));
Vue.component('special-action-jump-to-form', require('./components/SpecialActionJumpToForm.vue'));

const app = new Vue({
  el: '#app',
  data: {
    hide_photo: true,
    photo_items: (window.Backend.photo_items) ? window.Backend.photo_items : ''
  },
  created() {
    this.$on('onShowPhoto', () => {
      this.hide_photo = false
      console.log('show photos')
    }),
    this.$on('onHidePhoto', () => {
      this.hide_photo = true
    })
  }
});
