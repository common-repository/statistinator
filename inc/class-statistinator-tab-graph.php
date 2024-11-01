<?php 

class Statistinator_Tab_Graph {

    public function __construct() {
    }

    public function render() {
        $status = get_option( 'statistinator_status' );
        $path = plugin_dir_url( STS_FILE );
?>
        <div class='sts-graph-container'>
<?php switch ( $status['global'] ) : ?>
<?php case 'initial': ?>
    <h2>Social Media Data</h2>
    <p> There is no data yet. </p>
    <p> You need to authorize Statistinator to access your data. Click on Authentication. </p>
    <a href="?page=statistinator&tab=authentication" class='button button-primary'>Authentication</a>
<?php break; ?>
<?php case 'authenticated': ?>
    <h2>Social Media Data</h2>
    <p> There is no data yet. </p>
    <p> You need to fill in your Social Accounts. Click on Social Accounts. </p>
    <a href="?page=statistinator&tab=account" class='button button-primary'>Social Accounts</a>
<?php break; ?>
<?php case 'enabled': ?>
            <?php 
        $data = Statistinator::get_latest_data();
        $final = array(
            'google' => array('title' => 'Google Analytics', 'data' => $data['google'], 'metric' => 'views'),
            'facebook' => array('title' => 'Facebook', 'data' => $data['facebook'], 'metric' => 'likes'),
            'twitter' => array('title' => 'Twitter', 'data' => $data['twitter'], 'metric' => 'followers'),
            'mailchimp' => array('title' => 'Mailchimp', 'data' => $data['mailchimp'], 'metric' => 'subscribers'),
            'youtube' => array('title' => 'YouTube', 'data' => $data['youtube'], 'metric' => 'subscribers')
        );
?>
            <div class="sts-left">
                <canvas id="sts-graph" width="400" height="400"></canvas>
            </div>
            <div class="sts-right">
                <h2>Social Media Data</h2>
                <p>The Social Media Data of the past month.</p>
                <div class="sts-latest">
                    <?php foreach ( $final as $name => $info ) : ?>
                    <?php if ( $status[ $name ] != 'enabled' ) continue; ?>
                    <div>
                        <div class="top">
                        <img src="<?= $path ?>images/<?= $name ?>.png" width="20" />
                            <h4><?= $info['title'] ?></h4>
                        </div>
                        <span class="metric"><?= $info['data'] ?></span>
                        <span class="description"><?= $info['metric'] ?></span>
                        <?php if ( $info['data'] == 0 ) : ?>
                        <p class="sts-error-message"><?= $info['title'] ?> might not be set up correctly. Please update your settings <a href="?page=statistinator&tab=account">here</a></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endswitch; ?>
        </div>
<?php
    }

}
