<template>
    <div class="photo-frame">
        <form style="margin:unset;">
            <img :src="photo_item.signed_url" class="img-fluid border border-secondary rounded " alt="Responsive image"
            />
            <button type="button" class="btn btn-info">上传时间： {{ photo_item.updated_at }}</button> 

            <div v-if="photo_item.uploader == user_openid">
                <button type="button" class="btn btn-danger" data-toggle="modal" 
                        :data-target="'#confirmDeletePhoto' + photo_item.id">删除照片</button>

                <general-confirm-delete-photo :photo-id="photo_item.id"></general-confirm-delete-photo>

            </div>
        </form>
    </div>
</template>

<script>
export default {
  props: [
    'user_openid', 'photo_item'
  ]
};
</script>