# WebStream
WebStreamはMVCアーキテクチャをベースとしたWebアプリケーションフレームワークです。
さらにS(Service)層を追加した4層構造のアーキテクチャとなっています。

##WebStreamのアーキテクチャ
WebStreamはMVCを拡張したアーキテクチャを採用しており、Serviceレイヤを追加しています。
MVCはFat Controller/Fat Model問題を引き起こしやすいアーキテクチャであるため、ビジネスロジックはServiceに定義します。
また、View内でビジネスロジックを書く場合はHelperを利用し、Viewはレンダリングに専念させます。  


##[Controller](#controller)
Contollerではクライアントからのリクエストを受け付け、ServiceまたはModelを呼び出します。
Controllerの処理が完了したらViewを呼び出します。Viewへパラメータを渡す場合、Serviceにセットします。
原則的にControllerにビジネスロジックを記述してはなりません。
`app/controllers`に`WebStream\Core\CoreController`クラスを継承したクラスを定義します。  

###Controllerクラスの定義
Controllerクラスは`\WebStream\Core\CoreController`クラスを継承します。
ControllerクラスからはServiceクラスまたはModelクラスを参照できます。またViewテンプレートを呼び出して描画できます。

####Serviceクラス、Modelクラス呼び出し
Serviceクラス、Modelクラスは以下のように呼び出します。

    namespace MyBlog;
    use WebStream\Core\CoreController;
    class BlogController extends CoreController {
        public funciton execute() {
            // $this->{ページ名}->(Service|Modelクラスのメソッド)
            $this->Blog->entry();
        }
    }

Controllerクラス内の`$this->{ページ名}`オブジェクトにはServiceクラスまたはModelクラスのインスタンスが格納されています。Serviceクラスを定義している場合はServiceクラスインスタンスが格納されます。このときのページ名はアッパーキャメルケースで指定します。
Serviceクラスを定義せずModelクラスのみ定義した場合はModelクラスインスタンスが格納されます。Serviceクラスに特段のビジネスロジックを記述する必要がなく、DBからのデータを取り出したいだけの場合など、Controllerクラスから直接Modelクラスにアクセスすることができます。
Controllerクラスでは[アノテーション](#annotaion)を使ってメソッドやプロパティを操作できます。

####Viewテンプレート呼び出し
HTMLを描画するにはControllerからViewテンプレートを呼び出します。Viewテンプレート呼び出しは[アノテーション](#annotaion)を利用します。

    namespace MyBlog;
    use WebStream\Core\CoreController;

    /**
     * @Inject
     * @Template("index.tmpl")
     */
    class BlogController extends CoreController {
        public funciton execute() {
            // $this->{ページ名}->(Service|Modelクラスのメソッド)
            $this->Blog->entry();
        }
    }

この処理で`@Template`に指定したテンプレートファイル`index.tmpl`を呼び出します。
テンプレートファイルは`app/views/(ページ名)/`に保存します。このときのページ名はスネークケースで指定します(詳細は[View](#view)で説明します)。


##[Service](#service)
ServiceクラスではContollerクラスから受け取ったリクエストやデータを使って処理をしたり、View経由でビジネスロジックを実行します。
メインとなるビジネスロジックはServiceに記述します。データベースへの問い合わせが必要な場合はModelへ問い合わせます。
また、Serviceでは開発者が個別に定義したクラス(ライブラリ)を利用することができます。Serviceで処理するロジックがない場合などはServiceを定義する必要はありません。
`app/services`に`WebStream\Core\CoreService`クラスを継承したクラスを定義します。  

    namespace MyBlog;
    use WebStream\Core\CoreService;
    class BlogService extends CoreService {
        public funciton entry() {
            // $this->{ページ名}->(Modelクラスのメソッド)
            $this->Blog->getEntryData();
        }
    }

Serviceクラス内の`$this->{ページ名}`オブジェクトにはModelクラスのインスタンスが格納されています。ModelクラスにアクセスしてDB処理を実行します。
また、ServiceクラスにはContoller、Service、Model、Helperの各クラスに属さないユーザ定義クラスへのパスが通っています。`app/libraries/`ディレクトリに任意のクラスを定義することでServiceクラスからアクセスできます。例えば、外部APIにアクセスするクラスや、データをバインドするEntityクラスなど特定用途のクラスはlibrariesに定義してください。


##[Model](#model)
ModelクラスはControllerクラス、ServiceクラスまたはViewクラスからのリクエストや受け取ったデータを元にデータベースに問い合わせます。
Serviceクラスが定義されない場合はController、Viewから直接呼び出されます。Modelにはデータベース問い合わせ処理を記述します。
`app/models`に`WebStream\Core\CoreModel`クラスを継承したクラスを定義します。  

    namespace MyBlog;
    use WebStream\Core\CoreModel;
    /**
     * @Inject
     * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
     */
    class BlogModel extends CoreModel {
        public funciton getEntryData() {
            $sql = "SELECT * FROM T_Blog";
            return $this->select($sql);
        }
    }

外部変数をパラメータに指定するには`$bind`変数にパラメータをセットします。
`$bind`変数には連想配列でプリペアードステートメントに設定する値を指定します。
データベース接続設定はクラスに[アノテーション](#annotaion)を指定します。

    namespace MyBlog;
    use WebStream\Core\CoreModel;
    /**
     * @Inject
     * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
     */
    class BlogModel extends CoreModel {
        public funciton getEntryData() {
            $sql = "SELECT * FROM T_Blog WHERE id = :id";
            $bind = ["id" => 10];
            return $this->select($sql, $bind);
        }
    }

Modelクラスでは以下のメソッドが利用可能です。

####Modelで利用可能なメソッド一覧
メソッド|内容
-------|----
select(string $sql)<br>select(string $sql, array $bind)|SELECTを実行する。
insert(string $sql, array $bind)|INSERTを実行する。
update(string $sql, array $bind)|UPDATEを実行する。
delete(string $sql)<br>delete(string $sql, array $bind)|DELETEを実行する。
beginTransation()|トランザクションを開始する。
commit()|コミットする。
rollback()|ロールバックする。
connect()|DBに接続する。
disconnect()|DBを切断する。

####クエリファイルによるSQL実行
Modelクラスでは直接SQLをメソッド内に記述する以外に、クエリファイル(XML)を使ってSQLを実行できます。クエリファイルは`query/`に保存します。

    <?xml version="1.0" encoding="utf-8"?>
    <!DOCTYPE mapper PUBLIC
      "-//github.com/mapserver2007//DTD Mapper 3.0//EN" "http://localhost/webstream-model-mapper.dtd">
    <mapper namespace="MyBlog">
      <select id="getData">
        SELECT
            *
        FROM
            T_Blog
        WHERE
            id = :id
      </select>
    </mapper>

クエリファイルのDTDは同ディレクトリに配置し、DOCTYPEの値は適宜修正しDTDを指すようにします。
mapperタグの`namespace`にModelクラスの名前空間を指定します。名前空間が一致すればModelクラスからクエリファイルを呼び出すことができます。
mapperタグ配下にSQLを記述するタグを記述します。`<select>`、`<insert>`、`<update>`、`<delete>`タグが指定可能です。タグの`id`をModelクラスのメソッドからアクセスするとSQLを実行できます。

    namespace MyBlog;
    use WebStream\Core\CoreModel;
    /**
     * @Inject
     * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
     */
    class BlogModel extends CoreModel {
        /**
         * @Inject
         * @Query(file="query/myblog.xml")
         */
        public funciton getEntryData() {
            $bind = ["id" => 10];
            return $this->getData($bind);
        }
    }

[アノテーション](#annotaion)を使い、クエリファイルパスを指定します。これによりクエリファイルに記述したSQLが自動的に紐付けられます。

####トランザクション処理
`$this->beginTransation()`でトランザクションを開始し`$this->commit()`でコミット、`$this->rollback()`でロールバックを実行します。
ただし、DBMSがトランザクション処理に対応していない場合はトランザクション処理は有効になりません。
なお、トランザクション処理を明示しない場合、処理が終了後、自動的にコミットを実行します。

##[View](#view)
Viewは画面に出力するHTMLなどを描画し、Controllerクラスから呼ばれます。HTML等の描画はWebStream独自のテンプレート機能を利用します。
ViewからはHelperまたはModel、Serviceを呼び出してビジネスロジックを実行することができます。
テンプレートファイルは`.tmpl`拡張子を付け、`app/views`にページ名をスネークケースに変換したフォルダを作成し保存します。
`__cache`、`__public`、`__shared`フォルダを作成すると、それぞれテンプレートキャッシュファイル、静的ファイル、共通テンプレートファイルを使用することができます。
ViewにはModel/Serviceオブジェクトが渡されるので、Model、Serviceで取得した値やビジネスロジックの実行がViewで可能になります。
Model/Serviceオブジェクトは`$model`変数に格納されます。また、Helperオブジェクトは`$helper`変数に格納されます。

ContollerクラスからViewテンプレートを呼び出します。`@Template`の仕様は[アノテーション](#annotaion)を参照してください。


    namespace MyBlog;
    use WebStream\Core\CoreController;

    /**
     * テンプレートを呼び出す。
     * @Inject
     * @Template("index.tmpl")
     */
    class BlogController extends CoreController {
        public funciton execute() {
            $this->Blog->entry();
        }
    }

`__shared`に保存した共通テンプレートを呼び出すことができます。
共通点プレートはheaderやfooterなど共通になる部分を定義するときに使用します。

    namespace MyBlog;
    use WebStream\Core\CoreController;

    /**
     * 基本テンプレートと共通テンプレートを呼び出す。
     * @Inject
     * @Template("index.tmpl")
     * @Template("common.tmpl", name="common", type="shared")
     */
    class BlogController extends CoreController {
        public funciton execute() {
            $this->Blog->entry();
        }
    }

共通テンプレートにするほどではないが、テンプレートを部品化したい場合、部分テンプレートとして呼び出すことができます。

    namespace MyBlog;
    use WebStream\Core\CoreController;

    /**
     * 基本テンプレートと共通テンプレートを呼び出す。
     * @Inject
     * @Template("index.tmpl")
     * @Template("side.tmpl", name="side_menu", type="parts")
     */
    class BlogController extends CoreController {
        public funciton execute() {
            $this->Blog->entry();
        }
    }

ViewテンプレートにはHTMLを記述しますが、Service/Modelの値などを埋め込むことができます。


    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <head>
            <title>%H{$model->getTitle()}</title>
        </head>
        <body>
            <div>%H{$model->getContent()}</div>
            %T{$common}
        </body>
    </html>

`$model`にアクセスするとServiceクラスまたはModelクラスにアクセスできます。また、`@Template`の`name`属性に指定した名前は変数としてアクセスできます。
Viewテンプレートでは以下の構文が使用可能です。

###[Viewテンプレート構文](#template_keyword)
構文|説明
---|----
%P{$hoge}|%P{}で囲ったPHPのコードを実行する。関数を実行する場合などに使用する。<br>ただし、関数の実行結果をreturnしても画面表示されない。また、関数内部で`echo`で標準出力した場合はエスケープされないので注意。<br>実行結果を伴わない関数の実行が必要な場合に使用する。
%H{$hoge}|%H{}で囲った変数を安全な値にエスケープしてHTMLとして表示する。関数を実行する場合も使用可能で、returnで返却された結果をエスケープして画面表示する。<br>ただし、関数内部で`echo`で標準出力した場合はエスケープされないので注意。
%J{$hoge}|%J{}で囲った変数を安全な値にエスケープしてJavaScriptコードとして評価する。関数を実行する場合も使用可能で、returnで返却された結果をエスケープして画面表示する。<br>ただし、関数内部で`echo`で標準出力した場合はエスケープされないので注意。
%X{$hoge}|%X{}で囲った変数を安全な値にエスケープしてXMLとして評価する。関数を実行する場合も使用可能で、returnで返却された結果をエスケープして画面表示する。<br>ただし、関数内部で`echo`で標準出力した場合はエスケープされないので注意。
%T{$template}|%T{}で囲ったテンプレートパスを読み込む。

##[Helper](#helper)
Viewの描画に関するロジックが必要な場合はHelperを呼び出します。
Helperクラスは`app/helpers`に`WebStream\Core\CoreHelper`クラスを継承したクラスを定義します。

    namespace WebStream\Test\TestData\Sample\App\Helper;

    use WebStream\Core\CoreHelper;
    use WebStream\Core\CoreService;

    class TestHelperHelper extends CoreHelper
    {
        public function help1()
        {
 	         return $this->help2($model->getName());
        }
    }

Helperクラス内ではViewテンプレート内と同様に`$model`オブジェクトからModelクラス、Serviceクラスを呼び出すことができます。
Helperクラスのメソッドは`$helper`オブジェクトにより呼び出します。

	$helper->method();

メソッド呼び出しにより、Viewテンプレートで必要なロジックを実行します。
メソッドの戻り値はViewテンプレートに描画されますが、<a href="#template_keyword">Viewテンプレート構文</a>によりエスケープして出力し、安全な値として出力する必要があります。

    %H{$helper->method()}

ただし、Helper内で直接echoで出力するとエスケープされないので注意してください。

##[Library](#library)
Libraryクラスは開発者が自由に定義できるクラスです。クラスは`app/libraries`に保存します。
例えば汎用的なクラスなどを個別のクラスに切り出したい場合に定義します。Libraryクラスのクラス名、ファイル名に制限はありません。定義したクラスは`Service`クラスからのみ参照可能です。

    namespace MyBlog;
    use WebStream\Core\CoreService;
    class BlogService extends CoreService {
        public funciton getWeather() {
            // libraries/Weather.phpを呼び出す
            $weather = new Weather();
        }
    }

定義したクラスは自動的にクラスパスが通っているのでインスタンス化することができます。



## [命名規則まとめ](#naming_rule)
各クラスの命名規則、保存場所のまとめは以下のとおりです。

レイヤ|サンプルクラス名|保存場所
-----|-------------|------
Controller|SampleController|app/controllers/SampleController.php
Service|SampleService|app/services/SampleService.php
Model|SampleModel|app/models/SampleModel.php
View|(任意の名前).tmpl|app/views/sample/(任意の名前).tmpl
Helper|SampleHelper|app/helpers/SampleHelper.php
Library|(任意の名前)|app/libraries/(任意の名前).php


##[ルーティング定義](#routing)
###routes.php
ルーティング設定により、URI設計を行うことができます。ルーティングにはmod_rewiteが必要です。  
ルーティング定義は`config/routes.php`に記述します。  

    namespace WebStream\Router;
    Router::setRule([
        '/login' => 'sample#login'
        '/blog/:id' => 'blog#entry'
    ]);

ルーティングルールは配列で定義し、キーにURIパス定義、バリューにクラス、アクション定義を記述します。誤った定義が記述された場合、例外が発生します。

###URIパス定義
URIパスは`/path/to`形式で定義します。またURIには変数の設定が可能で、`:value`形式で記述します。例えば、`/blog/:id`と定義し、`/blog/10`にアクセスした場合、Controllerクラスでは以下の方法で値を取得出来ます。

    namespace MyBlog;
    use WebStream\Core\CoreController;
    class BlogController extends CoreController {
        public function execute(array $params) {
            $id = $params['id']; // 10
        }
    }

##バリデーション定義

###validates.php
バリデーション設定により、GET/POST/PUT/DELETEリクエストに含まれるパラメータをチェックすることができます。  
バリデーション定義は`config/validates.php`に記述します。  

    namespace WebStream\Validator;
    Validator::setRule([
        "sample#validateForm" => [
            "post#name" => "required",
            "get#page"  => "required|number"
        ]
    ]);

Validator::setRule内にバリデーション定義を記述します。
定義は、キーにクラス#アクション、バリューにバリデーション内容を記述します。バリデーション内容もキー、バリュー形式になっており、キーにリクエストメソッド#パラメータ名、バリューにチェックルールを記述します。

####バリデーションチェックルール

ルール        |内容
-------------|---------
required     |必須チェック
number       |数値チェック(整数)
min[n]       |最小値チェック(整数)
max[n]       |最大値チェック(整数)
min_length[n]|最小文字数チェック(整数)
max_length[n]|最大文字数チェック(整数)
equal        |文字列一致チェック
length       |文字数一致チェック
range[n..m]  |範囲チェック(整数)
regexp[//]   |正規表現チェック

##リクエストパラメータ
GET/POST/PUT/DELETEで送信した値をControllerで取得できます。
`$this->request`オブジェクトからリクエストパラメータを取得でき、`get`,`post`,`put`,`delete`メソッドにそれぞれアクセスします。

    namespace MyBlog;
    use WebStream\Core\CoreController;
    class BlogController extends CoreController {
        public function execute() {
            $getParams = $this->request->get(); // GETパラメータすべて取得
            $getParam  = $this->request->get("name");
        }
    }

##セッション
ログイン処理などを実装するときに、セッション管理を使用しますが、WebStreamでは`$this->session`オブジェクトを使用します。セッション期限を指定するには`restart`メソッドを使用します。

    namespace MyBlog;
    use WebStream\Core\CoreController;
    class LoginController extends CoreController {
        public function execute() {
            $expire = 6000; // 10分
            $path = "/login";
            $domain = ".mydomain.com";
            $getParams = $this->session->restart($expire, $path, $domain);
        }
    }

セッションがタイムアウトした場合、`SessionTimeoutException`が発生します。

##<a href="annotaion"> アノテーション </a>
ControllerとModelではアノテーションを使ってクラスやメソッドを操作することができます。アノテーションを利用することで便利な処理が可能になります。
クラスまたはメソッドに対するアノテーションは`@Inject`、プロパティに対するアノテーションは`@Autowired`の指定が必須です。


アノテーション|説明
-----------|----
@Inject    |メソッドに対するアノテーションを有効にする
@Autowired |プロパティに対するアノテーションを有効にする


####Controllerで使用可能なアノテーション
アノテーション|説明|サンプル
-----------|----|------
@Value     |プロパティに初期値(文字列、数値)を設定する|@Value(10)<br>@Value("hoge")
@Type      |プロパティに指定した型で初期化する|@Type("\MyBlog\Entity")
@Filter    |アクションメソッドが呼ばれる前または後に任意の処理を実行する|@Filter(type="before")<br>@Filter(type="after")<br>@Filter(type="before" except="method1")<br>@Filter(type="before" only="method2")<br>@Filter(type="before",only="method1",except="method2")<br>@Filter(type="after",except={"method1","method2"})
@Header    |リクエスト/レスポンスを制御する|@Header(contentType="html")<br>@Header(contentType="xml")<br>@Header(allowMethod="POST")<br>@Header(allowMethod={"GET","POST"})
@Template  |Viewテンプレートを設定する|@Template("index.tmpl")<br>@Template("index.tmpl",name="head" type="parts")<br>@Template("index.tmpl",name="shared",type="shared")
@TemplateCache|テンプレートをキャッシュする時間を指定|@TemplateCache(expire=3600)
@ExceptionHandler|例外を補足して別処理を実行する|@ExceptionHandler("\WebStream\Exception\SessionTimeoutException")<br>@ExceptionHandler({"\WebStream\Exception\ResourceNotFoundException","\WebStream\Exception\ClassNotFoundException"})

####Modelで使用可能なアノテーション
アノテーション|説明|サンプル
-----------|----|------
@Database  |Modelクラスに対してデータベース設定をする|@Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
@Query     |読み込むクエリファイルを指定する|@Query(file="query/blog_query.xml")
