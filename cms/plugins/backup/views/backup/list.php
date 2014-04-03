<table class="table table-striped table-hover">
    <colgroup>
        <col width="150px" />
        <col />
        <col width="90px" />
        <col width="150px" />
    </colgroup>
    <thead>
        <tr>
            <th><?php echo __('Created'); ?></th>
            <th><?php echo __('File name'); ?></th>
            <th><?php echo __('File size'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($files as $filename => $data): ?>
    <tr>
        <td><?php echo $data['date']; ?></td>
        <th>
            <?php echo HTML::anchor(Route::get('backend')->uri(array(
                'controller' => 'backup',
                'action' => 'view', 'id' => $filename
            )), $filename, array('class' => 'popup fancybox.iframe')); ?>
        </th>
        <td><?php echo $data['size']; ?></td>
        <td>
            <?php echo UI::button(NULL, array(
                'class' => 'btn', 
                'href' => Route::get('downloader')->uri(array(
                    'path' => Download::secure_path( BACKUP_PLUGIN_FOLDER . $filename)
                )),
                'icon' => UI::icon( 'download' )
            ));?>
            <?php echo UI::button(NULL, array(
                'class' => 'btn btn-mini btn-success btn-confirm', 
                'href' => Route::get('backend')->uri(array(
                    'controller' => 'backup',
                    'action' => 'restore', 'id' => $filename
                )), 
                'icon' => UI::icon( 'off icon-white' )
            ));?> 
            <?php echo UI::button(NULL, array(
                'class' => 'btn btn-mini btn-danger btn-confirm', 
                'href' => Route::get('backend')->uri(array(
                    'controller' => 'backup',
                    'action' => 'delete', 'id' => $filename
                )), 
                'icon' => UI::icon( 'trash icon-white' )
            ));
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>