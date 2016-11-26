<?php
/**
 * @package  FS_CURL
 * @subpackage FS_CURL_Configuration
 * callcenter.conf.php
 */
/**
 * @package  FS_CURL
 * @subpackage FS_CURL_Configuration
 * @license
 * @author Raymond Chandler (intralanman) <intralanman@gmail.com>, Aleksandr Popov <sandpaper@yandex.ru>
 * @version 0.1
 * Write XML for callcenter.conf
*/
class callcenter_conf extends fs_configuration {
    public function callcenter_conf() {
        $this -> fs_configuration();
    }

    public function main() {
        $this -> xmlw -> startElement('configuration');
        $this -> xmlw -> writeAttribute('name', basename(__FILE__, '.php'));
        $this -> xmlw -> writeAttribute('description', 'Calcenters');
        $this -> write_settings();
        $this -> write_queues();
        $this -> xmlw -> endElement();
    }

    /**
	 * Write the <settings> for the current profile
	 * @param integer $profile_id id of the callcenter profile in callcenter_conf
	 */
    private function write_settings() {
        $query = "SELECT * FROM callcenter_settings";
        $settings_array = $this -> db -> queryAll($query);
        $settings_count = count($settings_array);
        if (FS_PDO::isError($settings_array)) {
            $this -> comment($query);
            $this -> comment($this -> db -> getMessage());
            return ;
        }
        if ($settings_count < 1) {
            return ;
        }
        $this -> xmlw -> startElement('settings');

        for ($i=0; $i<$settings_count; $i++) {
            //$this -> comment_array($settings_array[$i]);
            $this -> xmlw -> startElement('param');
            $this -> xmlw -> writeAttribute('name', $settings_array[$i]['param_name']);
            $this -> xmlw -> writeAttribute('value', $settings_array[$i]['param_value']);
            $this -> xmlw -> endElement();//</param>
        }
        $this -> xmlw -> endElement();
    }

    /**
	 * Write <queues> XML for current profile
	 * @param integer $profile_id id of the callcenter profile in callcenter_conf
	 */
    private function write_queues() {
        $query = "SELECT * FROM callcenter_queues "
        . "ORDER BY queue_name, queue_param";
        $queue_array = $this -> db -> queryAll($query);
        $queue_count = count($queue_array);
        //$this -> comment_array($queue_array);
        if (FS_PDO::isError($queue_array)) {
            $this -> comment($query);
            $this -> comment($this -> db -> getMessage());
            return ;
        }
        if ($queue_count < 1) {
            return ;
        }
        $this -> xmlw -> startElement('queues');
        for ($i=0; $i<$queue_count; $i++) {
            $this_queue = $queue_array[$i]['queue_name'];
            if (!array_key_exists($i-1, $queue_array) || $this_queue != $queue_array[$i-1]['queue_name']) {
                $this -> xmlw -> startElement('queue');
                $this -> xmlw -> writeAttribute('name', $this_queue);
            }
            $this -> xmlw -> startElement('param');
            $this -> xmlw -> writeAttribute('name', $queue_array[$i]['queue_param']);
            $this -> xmlw -> writeAttribute('value', $queue_array[$i]['queue_value']);
            $this -> xmlw -> endElement();
            if (!array_key_exists($i+1, $queue_array)
            || $this_queue != $queue_array[$i+1]['queue_name']) {
                $this -> xmlw -> endElement();
            }
            $last_queue = $this_queue;
        }
        $this -> xmlw -> endElement(); 
    }

}

?>
