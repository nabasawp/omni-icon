<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\VarDumper\Cloner;

use OmniIconDeps\Symfony\Component\VarDumper\Caster\Caster;
use OmniIconDeps\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static array $defaultCasters = ['__PHP_Incomplete_Class' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\Caster', 'castPhpIncompleteClass'], 'AddressInfo' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\AddressInfoCaster', 'castAddressInfo'], 'Socket' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SocketCaster', 'castSocket'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\CutStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\CutArrayStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castCutArray'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\ConstStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\EnumStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castEnum'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\ScalarStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castScalar'], 'Fiber' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\FiberCaster', 'castFiber'], 'Closure' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClosure'], 'Generator' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castZendExtension'], 'OmniIconDeps\Doctrine\Common\Persistence\ObjectManager' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\Doctrine\Common\Proxy\Proxy' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castCommonProxy'], 'OmniIconDeps\Doctrine\ORM\Proxy\Proxy' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castOrmProxy'], 'OmniIconDeps\Doctrine\ORM\PersistentCollection' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castPersistentCollection'], 'OmniIconDeps\Doctrine\Persistence\ObjectManager' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'DOMException' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'], 'OmniIconDeps\Dom\Exception' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'], 'DOMStringList' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNameList' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMImplementation' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'], 'OmniIconDeps\Dom\Implementation' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNode' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'OmniIconDeps\Dom\Node' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNameSpaceNode' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMDocument' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDocument'], 'OmniIconDeps\Dom\XMLDocument' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castXMLDocument'], 'OmniIconDeps\Dom\HTMLDocument' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castHTMLDocument'], 'DOMNodeList' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'OmniIconDeps\Dom\NodeList' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNamedNodeMap' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'OmniIconDeps\Dom\DTDNamedNodeMap' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMXPath' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'OmniIconDeps\Dom\XPath' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'OmniIconDeps\Dom\HTMLCollection' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'OmniIconDeps\Dom\TokenList' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'XMLReader' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castErrorException'], 'Exception' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castException'], 'Error' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castError'], 'OmniIconDeps\Symfony\Bridge\Monolog\Logger' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\Symfony\Component\DependencyInjection\ContainerInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\Symfony\Component\EventDispatcher\EventDispatcherInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\Symfony\Component\HttpClient\AmpHttpClient' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'], 'OmniIconDeps\Symfony\Component\HttpClient\CurlHttpClient' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'], 'OmniIconDeps\Symfony\Component\HttpClient\NativeHttpClient' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'], 'OmniIconDeps\Symfony\Component\HttpClient\Response\AmpResponse' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'OmniIconDeps\Symfony\Component\HttpClient\Response\AmpResponseV4' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'OmniIconDeps\Symfony\Component\HttpClient\Response\AmpResponseV5' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'OmniIconDeps\Symfony\Component\HttpClient\Response\CurlResponse' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'OmniIconDeps\Symfony\Component\HttpClient\Response\NativeResponse' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'OmniIconDeps\Symfony\Component\HttpFoundation\Request' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castRequest'], 'OmniIconDeps\Symfony\Component\Uid\Ulid' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUlid'], 'OmniIconDeps\Symfony\Component\Uid\Uuid' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUuid'], 'OmniIconDeps\Symfony\Component\VarExporter\Internal\LazyObjectState' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castLazyObjectState'], 'OmniIconDeps\Symfony\Component\VarDumper\Exception\ThrowingCasterException' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castThrowingCasterException'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\TraceStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castTraceStub'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\FrameStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFrameStub'], 'OmniIconDeps\Symfony\Component\VarDumper\Cloner\AbstractCloner' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\Symfony\Component\ErrorHandler\Exception\FlattenException' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFlattenException'], 'OmniIconDeps\Symfony\Component\ErrorHandler\Exception\SilencedErrorContext' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castSilencedErrorContext'], 'OmniIconDeps\Imagine\Image\ImageInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ImagineCaster', 'castImage'], 'OmniIconDeps\Ramsey\Uuid\UuidInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\UuidCaster', 'castRamseyUuid'], 'OmniIconDeps\ProxyManager\Proxy\ProxyInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\PHPUnit\Framework\MockObject\MockObject' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\PHPUnit\Framework\MockObject\Stub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\Prophecy\Prophecy\ProphecySubjectInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'OmniIconDeps\Mockery\MockInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'PDO' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdo'], 'PDOStatement' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileInfo'], 'SplFileObject' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileObject'], 'SplHeap' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'], 'SplObjectStorage' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'], 'OuterIterator' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castOuterIterator'], 'WeakMap' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakMap'], 'WeakReference' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakReference'], 'Redis' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'], 'OmniIconDeps\Relay\Relay' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'], 'RedisArray' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castDateTime'], 'DateInterval' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castInterval'], 'DateTimeZone' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castTimeZone'], 'DatePeriod' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castPeriod'], 'GMP' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\GmpCaster', 'castGmp'], 'MessageFormatter' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\MemcachedCaster', 'castMemcached'], 'OmniIconDeps\Ds\Collection' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castCollection'], 'OmniIconDeps\Ds\Map' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castMap'], 'OmniIconDeps\Ds\Pair' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castPair'], 'OmniIconDeps\Symfony\Component\VarDumper\Caster\DsPairStub' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castPairStub'], 'mysqli_driver' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\MysqliCaster', 'castMysqliDriver'], 'CurlHandle' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\CurlCaster', 'castCurl'], 'OmniIconDeps\Dba\Connection' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'], ':dba' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'], ':dba persistent' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'], 'GdImage' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\GdCaster', 'castGd'], 'SQLite3Result' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\SqliteCaster', 'castSqlite3Result'], 'OmniIconDeps\PgSql\Lob' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLargeObject'], 'OmniIconDeps\PgSql\Connection' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLink'], 'OmniIconDeps\PgSql\Result' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castResult'], ':process' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castProcess'], ':stream' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'], 'OpenSSLAsymmetricKey' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslAsymmetricKey'], 'OpenSSLCertificateSigningRequest' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslCsr'], 'OpenSSLCertificate' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslX509'], ':persistent stream' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'], ':stream-context' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'], 'RdKafka' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castRdKafka'], 'OmniIconDeps\RdKafka\Conf' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castConf'], 'OmniIconDeps\RdKafka\KafkaConsumer' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castKafkaConsumer'], 'OmniIconDeps\RdKafka\Metadata\Broker' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castBrokerMetadata'], 'OmniIconDeps\RdKafka\Metadata\Collection' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castCollectionMetadata'], 'OmniIconDeps\RdKafka\Metadata\Partition' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castPartitionMetadata'], 'OmniIconDeps\RdKafka\Metadata\Topic' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicMetadata'], 'OmniIconDeps\RdKafka\Message' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castMessage'], 'OmniIconDeps\RdKafka\Topic' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopic'], 'OmniIconDeps\RdKafka\TopicPartition' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicPartition'], 'OmniIconDeps\RdKafka\TopicConf' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicConf'], 'OmniIconDeps\FFI\CData' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData'], 'OmniIconDeps\FFI\CType' => ['OmniIconDeps\Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData']];
    protected int $maxItems = 2500;
    protected int $maxString = -1;
    protected int $minDepth = 1;
    /**
     * @var array<string, list<callable>>
     */
    private array $casters = [];
    /**
     * @var callable|null
     */
    private $prevErrorHandler;
    private array $classInfo = [];
    private int $filter = 0;
    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(?array $casters = null)
    {
        $this->addCasters($casters ?? static::$defaultCasters);
    }
    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or object types to a callback.
     * Use types as keys and callable casters as values.
     * Prefix types with `::`,
     * see e.g. self::$defaultCasters.
     *
     * @param array<string, callable> $casters A map of casters
     */
    public function addCasters(array $casters): void
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }
    /**
     * Adds default casters for resources and objects.
     *
     * Maps resources or object types to a callback.
     * Use types as keys and callable casters as values.
     * Prefix types with `::`,
     * see e.g. self::$defaultCasters.
     *
     * @param array<string, callable> $casters A map of casters
     */
    public static function addDefaultCasters(array $casters): void
    {
        self::$defaultCasters = [...self::$defaultCasters, ...$casters];
    }
    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }
    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString): void
    {
        $this->maxString = $maxString;
    }
    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth): void
    {
        $this->minDepth = $minDepth;
    }
    /**
     * Clones a PHP variable.
     *
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     */
    public function cloneVar(mixed $var, int $filter = 0): Data
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }
            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }
            return \false;
        });
        $this->filter = $filter;
        if ($gc = gc_enabled()) {
            gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                gc_enable();
            }
            restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }
    /**
     * Effectively clones the PHP variable.
     */
    abstract protected function doClone(mixed $var): array;
    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castObject(Stub $stub, bool $isNested): array
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (str_contains($class, "@anonymous\x00")) {
            $stub->class = get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = method_exists($class, '__debugInfo');
            foreach (class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';
            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castResource(Stub $stub, bool $isNested): array
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;
        try {
            if (!empty($this->casters[':' . $type])) {
                foreach ($this->casters[':' . $type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
