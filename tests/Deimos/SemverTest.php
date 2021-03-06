<?php

namespace Deimos;

class SemverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $string
     * @param $major
     * @param $minor
     * @param $patch
     * @param $preRelease
     * @param $build
     * @param $metadata
     *
     * @dataProvider providerSmv
     */
    public function testSmv($string, $major, $minor, $patch, $preRelease, $build, $metadata)
    {
        $sv = new Semver($string);
        $this->assertEquals($sv->getMajor(), $major);
        $this->assertEquals($sv->getMinor(), $minor);
        $this->assertEquals($sv->getPatch(), $patch);
        $this->assertEquals($sv->getBuild(), $build);
        $this->assertEquals($sv->getPreRelease(), $preRelease);
        $this->assertEquals($sv->getMetadata(), $metadata);
    }

    /**
     * @return array
     */
    public function providerSmv()
    {
        return array(
            array('v3.14.1592-beta2.+firefox', 3, 14, 1592, PreRelease::BETA, 2, 'firefox'),
            array('v3.14.1592-beta65+firefox', 3, 14, 1592, PreRelease::BETA, 65, 'firefox'),

            array('1', 1, 0, 0, PreRelease::STABLE, 0, null),
            array('1.1', 1, 1, 0, PreRelease::STABLE, 0, null),
            array('1.1.1', 1, 1, 1, PreRelease::STABLE, 0, null),

            array('1-alpha.1', 1, 0, 0, PreRelease::ALPHA, 1, null),
            array('1-alpha', 1, 0, 0, PreRelease::ALPHA, 0, null),
            array('1-rc', 1, 0, 0, PreRelease::RELEASE_CANDIDATE, 0, null),
            array('1-rc+hello-world', 1, 0, 0, PreRelease::RELEASE_CANDIDATE, 0, 'hello-world'),

            array('1.1-alpha.1', 1, 1, 0, PreRelease::ALPHA, 1, null),
            array('1.1-alpha', 1, 1, 0, PreRelease::ALPHA, 0, null),
            array('1.1-rc', 1, 1, 0, PreRelease::RELEASE_CANDIDATE, 0, null),
            array('1.1-rc+hello-world', 1, 1, 0, PreRelease::RELEASE_CANDIDATE, 0, 'hello-world'),

            array('1.0.1-alpha.1', 1, 0, 1, PreRelease::ALPHA, 1, null),
            array('1.0.1-alpha', 1, 0, 1, PreRelease::ALPHA, 0, null),
            array('1.0.1-rc', 1, 0, 1, PreRelease::RELEASE_CANDIDATE, 0, null),
            array('1.0.1-rc+hello-world', 1, 0, 1, PreRelease::RELEASE_CANDIDATE, 0, 'hello-world'),

            array('1.1-alpha.1', 1, 1, 0, PreRelease::ALPHA, 1, null),
            array('888345.111.123-alpha', 888345, 111, 123, PreRelease::ALPHA, 0, null),
            array('1.0.1-beta+pre-release', 1, 0, 1, PreRelease::BETA, 0, 'pre-release'),
            array('1.0.1-rc.88+hello-world', 1, 0, 1, PreRelease::RELEASE_CANDIDATE, 88, 'hello-world'),

            array('1-gm', 1, 0, 0, PreRelease::GOLD_MASTER, 0, null),
            array('1-gm+deimos-project', 1, 0, 0, PreRelease::GOLD_MASTER, 0, 'deimos-project'),

            array('1.1-gm', 1, 1, 0, PreRelease::GOLD_MASTER, 0, null),
            array('1.1-gold-master+deimos-project', 1, 1, 0, PreRelease::GOLD_MASTER, 0, 'deimos-project'),

            array('1.0.1-gm', 1, 0, 1, PreRelease::GOLD_MASTER, 0, null),
            array('1.0.1-gold-master+deimos-project', 1, 0, 1, PreRelease::GOLD_MASTER, 0, 'deimos-project'),

            array('1.0.1-gm.88+deimos-project', 1, 0, 1, PreRelease::GOLD_MASTER, 88, 'deimos-project'),
        );
    }

    /**
     * @param $semver1
     * @param $semver2
     * @param $greater
     * @param $less
     * @param $equal
     *
     * @dataProvider providerSmvCompare
     */
    public function testSmvCompare($semver1, $semver2, $greater, $less, $equal)
    {
        $semver1 = new Semver($semver1);
        $semver2 = new Semver($semver2);

        $this->assertTrue(Comparator::greaterThan($semver1, $semver2) === $greater);
        $this->assertTrue(Comparator::lessThan($semver1, $semver2) === $less);
        $this->assertTrue(Comparator::equalTo($semver1, $semver2) === $equal);

        $this->assertTrue(Comparator::greaterThanOrEqualTo($semver1, $semver2) === ($greater || $equal));
        $this->assertTrue(Comparator::lessThanOrEqualTo($semver1, $semver2) === ($less || $equal));

        $this->assertTrue(Comparator::notEqualTo($semver1, $semver2) === !$equal);
    }

    /**
     * @return array
     */
    public function providerSmvCompare()
    {
        return array(
            array('1.0.0', '1.0.1', false, true, false),
            array('1.0.1-rc', '1.0.1', false, true, false),

            array('1.0.0-rc.1', '1-rc.2', false, true, false),
            array('1.0.1-beta.1', '1.0.1-alpha.16', true, false, false),

            array('1.0.0-rc.1', '1-rc.1', false, false, true),

            array('1.0.0-rc.1', '1-rc1.+gold-master', false, false, true),

            array('1-beta.16', '1-alpha.16', true, false, false),
            array('1-beta16', '1-alpha.16', true, false, false),

            array('1-beta.16+hello', '1-alpha.16+world', true, false, false),

            array('1.999.113123-beta.16+hello', '1.999.113123-rc+world', false, true, false),

            array('v3.14.1592-beta2.+firefox', 'v3.14.1592-beta3.+firefox', false, true, false),
            array('v3.14.1592-beta65+firefox', 'v3.14.1592-beta65+firefox', false, false, true),
        );
    }

}
