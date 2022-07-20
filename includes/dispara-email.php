<?php


    include "quality/class.phpmailer.php";
    

 
    $nome_empresa = "Sabollan Química";
    $assunto_padrao = "Contato via ".$nome_empresa;
    $emailContato = "sabollanquimica0701@gmail.com";
    $smtp_contato            = "mail.qualitysmi.com.br";
    $email_remetente         = "clientes@qualitysmi.com.br";
    $senha_remetente         = "clientes@quali100";

   

    try {

        if(empty($nome_empresa)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>\$nome_empresa</strong> não foi definido.</p>");
        }
        if(empty($smtp_contato)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>\$smtp_contato</strong> não foi definido.</p>");
        }
        if(empty($email_remetente)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>\$email_remetente</strong> não foi definido.</p>");
        }
        if(empty($senha_remetente)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>\$senha_remetente</strong> não foi definido.</p>");
        }
        if(empty($emailContato)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>\$emailContato</strong> não foi definido.</p>");
        }

        $dados = filter_input(INPUT_POST, "data", FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

        $nome          = $dados["Contato"]["nome"];
        $email         = $dados["Contato"]["email"];
        $telefone      = $dados["Contato"]["telefone"];
        $como_conheceu = $dados["Contato"]["como_conheceu"];
        $mensagem      = $dados["Contato"]["mensagem"];
        $emails_status = false;
        
        if(empty($nome)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>Nome</strong> é um campo obrigatório.</p>");
        }
        if(empty($email)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>Email</strong> é um campo obrigatório.</p>");
        }
        if(empty($telefone)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>Telefone</strong> é um campo obrigatório.</p>");
        }
        if(empty($mensagem)){
            throw new Exception("<h3>Ocorreu um Erro</h3><p><strong>Mensagem</strong> é um campo obrigatório.</p>");
        }
        
        // Monta corpo da mensagem do email
        $conteudo  = "<h3>".$assunto_padrao."</h3>";
        $conteudo .= "<p><strong>Nome</strong>: " . $nome . "</p>";
        $conteudo .= "<p><strong>Email</strong>: " . $email . "</p>";
        $conteudo .= "<p><strong>Telefone</strong>: " . $telefone . "</p>";
        $conteudo .= "<p><strong>Como nos conheceu</strong>: " . $como_conheceu . "</p>";
        $conteudo .= "<p><strong>Mensagem</strong>: " . $mensagem . "</p>";
        
        if($_SERVER["SERVER_NAME"] == "localhost")
        {
            throw new Exception("<p>Servidor local para testes</p><hr>".$conteudo);
        }

        // Disparo com autenticação
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth  = true;
        $mail->Host      = $smtp_contato;
        $mail->Port      = 465;                
        $mail->SMTPSecure = "ssl";
        $mail->Username  = $email_remetente;
        $mail->Password  = $senha_remetente;
        $mail->From      = $email_remetente;
        $mail->FromName  = $nome;
        $mail->AddAddress($emailContato, $nome_empresa);
        $mail->AddReplyTo($email, $nome);

        // E-mails em cópia, adicionar uma nova linha para cada e-mail
        // $mail->addCC("email@qualitysmi.com.br");
        
        // E-mails em cópia oculta, adicionar uma nova linha para cada e-mail
        // $mail->addBCC("email@qualitysmi.com.br");
        
        $mail->isHTML(true);
        $mail->CharSet = "UTF-8";
        $mail->Subject  = $assunto_padrao;
        $mail->Body = $conteudo;

        if(!$mail->Send()) {
            // Disparo sem autenticação
            $header  = "MIME-Version: 1.1\n";
            $header .= "Content-type: text/html; charset=utf-8\n";
            $header .= "To: \"" . $nome_empresa . "\" <" . $emailContato . ">\n";
            $header .= "From: \"" . $nome . "\" <" . $email . ">\n";
            $header .= "Return-Path: \"" . $nome . "\" <" . $email . ">\n";
            $header .= "Reply-To: \"" . $nome . "\" <" . $email . ">\n";
            $mail = mail($emailContato, $assunto_padrao, $conteudo, $header, "-r". $emailContato);
            if($mail){
                $emails_status = true;
            }
        } else {
            $emails_status = true;
        }

        if($emails_status == false){
            throw new Exception("<h3>Ocorreu um Erro</h3><p>Ocorreu um erro no envio do email, por favor tente novamente.</p>");
        }
        
        echo json_encode(array(
            "status" => true,
            "message" => $conteudo
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            "status" => false,
            "message" => $e->getMessage()
        ));
    }