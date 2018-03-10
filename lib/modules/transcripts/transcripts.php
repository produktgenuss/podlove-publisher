<?php
namespace Podlove\Modules\Transcripts;

use Podlove\Modules\Transcripts\Model\Transcript;
use Podlove\Modules\Transcripts\Model\VoiceAssignment;
use Podlove\Model;

use Podlove\Webvtt\Parser;
use Podlove\Webvtt\ParserException;

class Transcripts extends \Podlove\Modules\Base {

	protected $module_name = 'Transcripts';
	protected $module_description = 'Manage transcripts, show them on your site and in the web player.';
	protected $module_group = 'metadata';

	public function load()
	{
		add_action('podlove_module_was_activated_transcripts', [$this, 'was_activated']);
		add_filter('podlove_episode_form_data', [$this, 'extend_episode_form'], 10, 2);
		add_action('wp_ajax_podlove_transcript_import', [$this, 'ajax_transcript_import']);
	}

	public function was_activated($module_name) {
		Transcript::build();
		VoiceAssignment::build();
	}

	public function extend_episode_form($form_data, $episode)
	{
		$form_data[] = array(
			'type' => 'callback',
			'key'  => 'transcripts',
			'options' => array(
				'callback' => function () use ($episode) {
					$data = '';
?>
<div id="podlove-transcripts-app-data" style="display: none"><?php echo $data ?></div>
<div id="podlove-transcripts-app"><transcripts></transcripts></div>
<?php
				},
				'label' => __( 'Transcripts', 'podlove-podcasting-plugin-for-wordpress' )
			),
			'position' => 425
		);
		return $form_data;
	}

	public function ajax_transcript_import()
	{
		if (!isset($_FILES['transcript'])) {
			wp_die();
		}

		// todo: I don't really want it permanently uploaded, so ... delete when done
		$file = wp_handle_upload($_FILES['transcript'], array('test_form' => false));
		
		if (!$file || isset($file['error'])) {
			$error = 'Could not upload transcript file. Reason: ' . $file['error'];
			\Podlove\Log::get()->addError($error);
			\Podlove\AJAX\Ajax::respond_with_json(['error' => $error]);
		}

		if (stripos($file['type'], 'vtt') === false) {
			$error = 'Transcript file must be webvtt. Is: ' . $file['type'];
			\Podlove\Log::get()->addError($error);
			\Podlove\AJAX\Ajax::respond_with_json(['error' => $error]);
		}

		$post_id = intval($_POST['post_id'], 10);
		$episode = Model\Episode::find_one_by_post_id($post_id);

		if (!$episode) {
			$error = 'Could not find episode for this post object.';
			\Podlove\Log::get()->addError($error);
			\Podlove\AJAX\Ajax::respond_with_json(['error' => $error]);
		}

		$content = file_get_contents($file['file']);

		$parser = new Parser();

		try {
			$result = $parser->parse($content);
		} catch (ParserException $e) {
			$error = 'Error parsing webvtt file: ' . $e->getMessage();
			\Podlove\Log::get()->addError($error);
			\Podlove\AJAX\Ajax::respond_with_json(['error' => $error]);
		}

		Transcript::delete_for_episode($episode->id);
		
		foreach ($result['cues'] as $cue) {
			$line = new Transcript;
			$line->episode_id = $episode->id;
			$line->start      = $cue['start'] * 1000;
			$line->end        = $cue['end'] * 1000;
			$line->voice      = $cue['voice'];
			$line->content    = $cue['text'];
			$line->save();
		}

		wp_die();
	}
}
