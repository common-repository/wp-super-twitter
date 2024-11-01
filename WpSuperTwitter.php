<?php

/*
  Plugin Name: WP Super Twitter
  Description: Listagem de twitters do usuário.
  Author: Claudney S. Reis <claudsan@gmail.com>, William Okano <williamokano@gmail.com>
  Version: 1.0
  Author URI: http://cucadigital.com.br/plugins
 */

require_once(ABSPATH . '/wp-admin/includes/plugin.php');
require_once(ABSPATH . WPINC . '/pluggable.php');

class WpSuperTwitter extends WP_Widget {

    function __construct() {
        parent::WP_Widget(false, 'Listar últimos Tweets');
    }

    /**
     * Salva os dados do widget no banco de dados
     *
     * @param array $nova_instancia Os novos dados do widget (a serem salvos)
     * @param array $instancia_antiga Os dados antigos do widget
     *
     * @return array $instancia Dados atualizados a serem salvos no banco de dados
     */
    public function update() {
        $opcoes['titulo'] = $_POST["titulo"];
        $opcoes['consumer_key'] = $_POST["consumer_key"];
        $opcoes['consumer_secret'] = $_POST["consumer_secret"];
        $opcoes['access_token'] = $_POST["access_token"];
        $opcoes['access_token_secret'] = $_POST["access_token_secret"];
        $opcoes['quantidade'] = $_POST["quantidade"];

        update_option('listaTwitter', $opcoes);
    }

    function widget($args) {
        $opcoes = get_option('listaTwitter');
        echo $args['before_widget'];
        echo $args['before_title'] . $opcoes['titulo'] . $args['after_title'];

        require dirname(__FILE__) . "/php-twitter/twitter.php";
        if (!empty($opcoes['consumer_key']) && !empty($opcoes['consumer_secret'])) {
            $a = new Twitter($opcoes['consumer_key'], $opcoes['consumer_secret']);
            $a->setOAuthToken($opcoes['access_token']);
            $a->setOAuthTokenSecret($opcoes['access_token_secret']);
            $data = $a->statusesUserTimeline();
            $max = count($data) < $opcoes['quantidade'] ? count($data) : $opcoes['quantidade'];
            echo "<ul>";
            for ($i = 0; $i < $max; $i++) {
                echo "<li>
		        <img src=\"{$data[$i]['user']['profile_image_url']}\" width=38 height=38/>
		        <a href='http://twitter.com/podesonhar/status/{$data[$i]['id']}'>";
                echo $data[$i]['text'];
                echo "</a></li>";
            }
            echo "<ul>";
        }
        echo $args['after_widget'];
    }

    /**
     * PAINEL DE CONFIGURACAO DO WIDGET
     */
    function form($instance) {
        //INICIALIZA AS VARIÁVEIS NECESSÁRIAS
        $opcoes = array();

        //CARREGAR AS OPÇÕES DESSE WIDGET
        $opcoes = get_option('listaTwitter');


        $consumer_key = "";
        $consumer_secret = "";
        $access_token = "";
        $access_token_secret = "";

        if (isset($opcoes['quantidade'])) {
            $consumer_key = $opcoes['consumer_key'];
        }

        if (isset($opcoes['quantidade'])) {
            $consumer_secret = $opcoes['consumer_secret'];
        }

        if (isset($opcoes['quantidade'])) {
            $access_token = $opcoes['access_token'];
        }

        if (isset($opcoes['quantidade'])) {
            $access_token_secret = $opcoes['access_token_secret'];
        }


        if (isset($opcoes['quantidade'])) {
            $qtdTweets = $opcoes['quantidade'];
        } else {
            $qtdTweets = 5;
        }

        if (isset($opcoes['titulo'])) {
            $titulo = $opcoes['titulo'];
        } else {
            $titulo = "Últimos tweets";
        }

        //FORMULÁRIO
        echo <<<FORM
                    <input type="hidden" name="salvarConfigurarListaTweets" value="1" />
                    <p>
                      <label for="usuario">Título:</label>
                      <input type="text" name="titulo" maxlength="26" value="{$titulo}" class="widefat" />
                      <label for="usuario">Consumer Key:</label>
                      <input type="text" name="consumer_key" maxlength="45" value="{$consumer_key}" class="widefat" />
                      <label for="consumer_secret">Consumer Secret:</label>
                      <input type="text" name="consumer_secret" maxlength="65" value="{$consumer_secret}" class="widefat" />
                      <label for="quantidade">Access Token:</label>
                      <input type="text" name="access_token" maxlength="65" value="{$access_token}" class="widefat" />
                      <label for="access_token_secret">Access Token Secret:</label>
                      <input type="text" name="access_token_secret" maxlength="65" value="{$access_token_secret}" class="widefat" />
                      <label for="quantidade">Quantidade:</label>
                      <input type="text" name="quantidade" maxlength="2" value="{$qtdTweets}" class="widefat" />
                    </p>
					<p>
						Para obter as chaves de acesso acesse aqui: <a href="https://dev.twitter.com/apps/new" target="_blank">https://dev.twitter.com/apps/new</a>
					</p>
FORM;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("WpSuperTwitter");'));
?>
