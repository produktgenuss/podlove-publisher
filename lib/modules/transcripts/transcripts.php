<?php
namespace Podlove\Modules\Transcripts;

use Podlove\Modules\Transcripts\Model\Transcript;
use Podlove\Modules\Transcripts\Model\VoiceAssignment;

class Transcripts extends \Podlove\Modules\Base {

	protected $module_name = 'Transcripts';
	protected $module_description = 'Manage transcripts, show them on your site and in the web player.';
	protected $module_group = 'metadata';

	public function load()
	{
		add_action('podlove_module_was_activated_transcripts', [$this, 'was_activated']);
	}

	public function was_activated($module_name) {
		Transcript::build();
		VoiceAssignment::build();
	}	
}
