<?hh


class Hh extends CI_Controller {

		public function __construct (): void {
			parent::__construct();
			$this->load->database();
		}

		public async function view($page = 'default_value'): Awaitable<void> {
// to return json:
//			$this->output->set_content_type('application/json');
//			$theHTMLResponse = <window>inside window, request_uri: {$_SERVER['REQUEST_URI']}</window>;
//			$this->output->set_output(json_encode(array('ShoppingCartHtml'=> $theHTMLResponse)));

// to return html directly:
//			echo <window>inside window, request_uri: {$_SERVER['REQUEST_URI']}</window>;
//			echo <p>Hello, world! XHP rocks!</p>;
//			echo <div>aaaaaaa parameter:{$page}</div>;
//			echo $this->genPage();
//			echo <introduction />;

// How to gen multiple
			// list ($content, $input) = await \HH\Asio\v(array(
			// 	$this->genContent(),
			// 	$this->genInput(),
			// ));


			$data = Map {};
			$data['title'] = 'learn english with cxf';
			$data['input'] = await $this->genInput();

			$this->load->view('main', $data->toArray());
    }

		private async  function genInput(): Awaitable<:xhp> {
			$content =
				<div>
					<script src={base_url() . 'js/app/handle_input.js'}></script>
					<input type="text" maxlength={500} id="new_sentence" placeholder="input new sentence"/>
					<button type="button" onclick="onClick_saveSentences()">{'save sentence'}</button>
				</div>;

			return $content;
		}
}
