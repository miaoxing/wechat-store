<?= $block->js() ?>
<script class="js-wechat-store-button-tpl" type="text/html">
  <button class="js-wechat-store-sync btn btn-secondary" type="button">同步微信门店</button>
</script>

<script>
  $('#shop-upload-form').append($('.js-wechat-store-button-tpl').html());
  $('.js-wechat-store-sync').click(function () {
    $.ajax({
      url: $.url('admin/wechat-stores/sync'),
      dataType: 'json',
      loading: true,
      success: function (ret) {
        $.alert(ret.message);
        if (ret.code === 1) {
          $('.js-shop-table').dataTable().reload();
        }
      }
    });
  });
</script>
<?= $block->end() ?>
