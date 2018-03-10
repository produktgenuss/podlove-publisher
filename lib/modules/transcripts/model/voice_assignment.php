<?php 
namespace Podlove\Modules\Transcripts\Model;

class VoiceAssignment extends \Podlove\Model\Base
{
	use \Podlove\Model\KeepsBlogReferenceTrait;

	public function __construct() {
		$this->set_blog_id();
	}
}

VoiceAssignment::property('id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
VoiceAssignment::property('episode_id', 'INT');
VoiceAssignment::property('voice', 'VARCHAR(255)');
VoiceAssignment::property('contributor_id', 'INT UNSIGNED');
