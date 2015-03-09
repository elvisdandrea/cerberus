<?php

/**
 * Class Messages
 *
 * This class centralizes the user messages
 * and can be used as Multi-language
 */

class Language {

    /**
     * The text library
     *
     * @var array
     */
    public static $_LANGUAGE = array(

        'pt' => array(
            'FATAL_ERROR_MESSAGE'   => 'Desculpe, o servidor nao executou corretamente! =(',
            'REST_NO_METHOD'        => 'Nao foi especificado um metodo',
            'METHOD_NOT_FOUND'      => 'O metodo [[1]] para [[0]] nao existe',
            'QUERY_ERROR'           => 'Desculpe, o servidor nao executou um acesso corretamente! =(',
            'USER_ALREADY_TAKEN'    => 'Usuário ou e-mail já existe!',
            'CANNOT_BE_BLANK'       => '[[0]] nao podem ser em branco!',
            'NOT_ACCEPTABLE'        => 'Requisicao nao aceita - [[0]]',
            'UNAUTHORIZED'          => 'Nao autorizado',
            'EXPIRED'               => 'Sessao Expirada'
        ),

        'en' => array(
            'FATAL_ERROR_MESSAGE'   => 'Sorry, The Server returned an exception! =(',
            'REST_NO_METHOD'        => 'No method specified',
            'METHOD_NOT_FOUND'      => 'The method [[1]] for [[0]] was not found',
            'QUERY_ERROR'           => 'Sorry, The Server returned an access exception! =(',
            'USER_ALREADY_TAKEN'    => 'Username or email already taken!',
            'CANNOT_BE_BLANK'       => '[[0]] cannot be blank!',
            'NOT_ACCEPTABLE'        => 'Request not acceptable - [[0]]',
            'UNAUTHORIZED'          => 'Not Authorized',
            'EXPIRED'               => 'Session Expired'
        )
    );

    /**
     * Returns the specified message or null
     * if message doesn't exists
     *
     * @param   $function       - The static called function name
     * @param   $arguments      - Not used
     * @return  string
     */
    public function __callStatic($function, $arguments) {
        $rep = array();
        if(isset($arguments) && count($arguments) > 0)
            foreach ($arguments as $key => $arg)
                $rep['[' . $key . ']'] = $arg;

        return !isset(self::$_LANGUAGE[LNG][$function]) ?: str_replace(array_keys($rep), array_values($rep),self::$_LANGUAGE[LNG][$function]);
    }

}