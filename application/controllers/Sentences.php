<?hh

define("THRIFT_ROOT", "/home/ubuntu/cpp_projects/im_server3/software/thrift-0.11.0/lib/php/lib/");
require_once THRIFT_ROOT . "Thrift/ClassLoader/ThriftClassLoader.php";


define("THRIFT_GEN", "/home/ubuntu/git_root/www/application/thrift_gen/");
require_once THRIFT_GEN . 'Something/gen-php/Something.php';
require_once THRIFT_GEN . 'Something/gen-php/Types.php';


use Thrift\ClassLoader\ThriftClassLoader;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TFramedTransport;
use Thrift\Exception\TException;

class Sentences extends CI_Controller {

	public function __construct (): void {
		parent::__construct();
		$this->load->database();
	}

	public async function getSentences(): Awaitable<void> {
		// http://192.168.1.111:20001/index.php?c=Sentences&m=getSentences

		$sql = "SELECT sentence FROM sentences";
		$query = $this->db->query($sql, array());

		$content = array();
		foreach ($query->result_array() as $row) {
			$content[] = $row['sentence'];
		}

		header('Access-Control-Allow-Origin: *'); // allow cross site call
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(Map {
					'error_code' => 0,
					'sentences' => $content,
					}));
	}

	public async function saveSentence(): Awaitable<void> {
		// the post data is at $_POST, or $this->input->post(), or $this->input->raw_input_stream
		header('Access-Control-Allow-Origin: *'); // allow cross site call

		$post = $_POST;
		if ($post === null) {
			$this->output->set_content_type('application/json');
			$theHTMLResponse = <window>inside window, request_uri: {$_SERVER['REQUEST_URI']}</window>;
			$this->output->set_output(json_encode(Map {
						'error_code' => 1,
						'affacted_rows'=> 0,
						}));
			return;
		}

		$input_sentence = idx($post, 'input');
		if ($input_sentence === '') {
			$this->output->set_content_type('application/json');
			$theHTMLResponse = <window>inside window, request_uri: {$_SERVER['REQUEST_URI']}</window>;
			$this->output->set_output(json_encode(Map {
						'error_code' => 2,
						'affacted_rows'=> 0,
						}));
			return;
		}

		$sql = "INSERT INTO sentences (sentence) VALUES (".$this->db->escape($input_sentence).")";
		$this->db->query($sql);
		$affacted_rows = $this->db->affected_rows();

		$this->output->set_content_type('application/json');
		$theHTMLResponse = <window>inside window, request_uri: {$_SERVER['REQUEST_URI']}</window>;
		$this->output->set_output(json_encode(Map {
					'error_code' => 0,
					'affacted_rows'=> $affacted_rows,
					}));
	}

	public async function testThrift(): Awaitable<void> {
		// http://192.168.1.111:20001/index.php?c=Sentences&m=testThrift		

		$loader = new ThriftClassLoader();
		$loader->registerNamespace('Thrift', THRIFT_ROOT);
		$loader->register();


		try {
			$socket = new TSocket('localhost', 9090);
			$transport = new TFramedTransport($socket);
			$protocol = new TBinaryProtocol($transport);
			$client = new SomethingClient($protocol);

			$transport->open();

			$ret = $client->ping();

			$transport->close();

		} catch (TException $tx) {
			print 'TException: '.$tx->getMessage()."\n";
		}
	}
}
