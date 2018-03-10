<?php 
namespace Podlove\Modules\Transcripts\Model;

class Transcript extends \Podlove\Model\Base
{
	use \Podlove\Model\KeepsBlogReferenceTrait;

	public function __construct() {
		$this->set_blog_id();
	}

	public static function delete_for_episode($episode_id)
	{
		global $wpdb;

		$sql = 'DELETE FROM '
		     . static::table_name()
		     . ' WHERE episode_id = ' . (int) $episode_id;

		return $wpdb->query($sql);
	}
}

Transcript::property('id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
Transcript::property('episode_id', 'INT');
Transcript::property('start', 'INT UNSIGNED');
Transcript::property('end', 'INT UNSIGNED');
Transcript::property('voice', 'VARCHAR(255)');
Transcript::property('content', 'TEXT');
