<?php
class FTP {
    const PATH_TEMP =  __DIR__ . "/temp";
    private $host;
    private $porta;
    private $usuario;
    private $senha;

    function __construct($host, $porta, $usuario, $senha){
        $this->host = $host;
        $this->porta = $porta;
        $this->usuario = $usuario;
        $this->senha = $senha;
    }

    public function conectarFTP()
    {
        $timeout = 9000; // Tempo em segundos para encerrar a conexão caso não haja resposta
        
        $ftp = ftp_connect( $this->$host, $this->$porta, $timeout ); // Retorno: true ou false
        if ( !$ftp ):
            return false;
        endif;
        $login = ftp_login( $ftp, $this->$usuario, $this->$senha ); // Retorno: true ou false
        if ( !$login ):
            return false;
        endif;
        return $ftp;
    }

    public function enviarArquivosPorFTP( $strArquivoLocal, $strArquivoRemoto )
    {
        $ftp = $this->conectarFTP();
        if ( !is_resource( $ftp ) ):
            return false;
        endif;

        if ( !is_file( $strArquivoLocal ) ):
            echo "<br/>Arquivo de origem '{$strArquivoLocal}' não existe!";
            return false;
        endif;
       
        // Alterna o modo de conexão para PASSIVO. No modo passivo, as conexões de dados são iniciadas pelo cliente, ao invés do servidor. Pode ser necessário se o cliente estiver atrás de um firewall.
        ftp_pasv( $ftp, true );

        // Faz o upload do arquivo no modo BINÁRIO (Deve ser FTP_ASCII ou FTP_BINARY.)
        $envio = ftp_put( $ftp, $strArquivoRemoto, $strArquivoLocal, FTP_BINARY ); // Retorno: true / false
        if ( $envio ): 
            echo "<br/>Arquivo '{$strArquivoLocal}' enviado para o FTP como '{$strArquivoRemoto}'!";
        else:
            echo "<br/>Falha ao enviar o arquivo {$strArquivoLocal} para o FTP!";
            ftp_close( $ftp ); // Fecha a conexão com o FTP
            return false;
        endif;
        ftp_close( $ftp ); // Fecha a conexão com o FTP
        return true;
    }

    public function baixarArquivosPorFTP( $strArquivoRemoto, $strArquivoLocal )
    {
        $ftp = $this->conectarFTP();
        if ( !is_resource( $ftp ) ):
            return false;
        endif;

        // Alterna o modo de conexão para PASSIVO. No modo passivo, as conexões de dados são iniciadas pelo cliente, ao invés do servidor. Pode ser necessário se o cliente estiver atrás de um firewall.
        ftp_pasv( $ftp, true );

        // Faz o download do arquivo no modo BINÁRIO (Deve ser FTP_ASCII ou FTP_BINARY.)
        $download = ftp_get( $ftp, $strArquivoLocal, $strArquivoRemoto, FTP_BINARY ); // Retorno: true / false
        if ( $download ):
            echo "<br/>Arquivo '{$strArquivoRemoto}' baixado com sucesso do FTP para '{$strArquivoLocal}'";
        else:
            echo "<br/>Falha ao baixar o arquivo {$strArquivoRemoto} do FTP!";
            ftp_close( $ftp ); // Fecha a conexão com o FTP
            return false;
        endif;
        ftp_close( $ftp ); // Fecha a conexão com o FTP
        $header = "Location: ".$strArquivoLocal;
        echo "<br/>{$header}";
        //header($header);
        return true;
    }
        
    public function listarArquivosPorFTP( $strDiretorio, $strBusca )
    {
        $ftp = $this->conectarFTP();
        if ( !is_resource( $ftp ) )
        {
            return false;
        }

        // Alterna o modo de conexão para PASSIVO. No modo passivo, as conexões de dados são iniciadas pelo cliente, ao invés do servidor. Pode ser necessário se o cliente estiver atrás de um firewall.
        ftp_pasv( $ftp, true );

        // Altera o diretório atual para "/arquivos/fotos/"
        ftp_chdir($ftp, $strDiretorio);

        // Lista os arquivos na forma de array()
        $arquivos = ftp_nlist($ftp, $strBusca);   
        ftp_close( $ftp ); // Fecha a conexão com o FTP
        return $arquivos;
    }

}