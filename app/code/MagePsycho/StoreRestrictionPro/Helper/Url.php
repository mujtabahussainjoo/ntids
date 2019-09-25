<?php

namespace MagePsycho\StoreRestrictionPro\Helper;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Url
{
    public function getBaseDomain($url)
    {
        $baseDomain = '';

        // generic tlds (source: http://en.wikipedia.org/wiki/Generic_top-level_domain)
        $gTld = [
            'biz',
            'com',
            'edu',
            'gov',
            'info',
            'int',
            'mil',
            'name',
            'net',
            'org',
            'aero',
            'asia',
            'cat',
            'coop',
            'jobs',
            'mobi',
            'museum',
            'pro',
            'tel',
            'travel',
            'arpa',
            'root',
            'berlin',
            'bzh',
            'cym',
            'gal',
            'geo',
            'kid',
            'kids',
            'lat',
            'mail',
            'nyc',
            'post',
            'sco',
            'web',
            'xxx',
            'nato',
            'example',
            'invalid',
            'localhost',
            'test',
            'bitnet',
            'csnet',
            'ip',
            'local',
            'onion',
            'uucp',
            'co' // note: not technically, but used in things like co.uk
        ];

        // country tlds (source: http://en.wikipedia.org/wiki/Country_code_top-level_domain)
        $cTld = [
            // active
            'ac',
            'ad',
            'ae',
            'af',
            'ag',
            'ai',
            'al',
            'am',
            'an',
            'ao',
            'aq',
            'ar',
            'as',
            'at',
            'au',
            'aw',
            'ax',
            'az',
            'ba',
            'bb',
            'bd',
            'be',
            'bf',
            'bg',
            'bh',
            'bi',
            'bj',
            'bm',
            'bn',
            'bo',
            'br',
            'bs',
            'bt',
            'bw',
            'by',
            'bz',
            'ca',
            'cc',
            'cd',
            'cf',
            'cg',
            'ch',
            'ci',
            'ck',
            'cl',
            'cm',
            'cn',
            'co',
            'cr',
            'cu',
            'cv',
            'cx',
            'cy',
            'cz',
            'de',
            'dj',
            'dk',
            'dm',
            'do',
            'dz',
            'ec',
            'ee',
            'eg',
            'er',
            'es',
            'et',
            'eu',
            'fi',
            'fj',
            'fk',
            'fm',
            'fo',
            'fr',
            'ga',
            'gd',
            'ge',
            'gf',
            'gg',
            'gh',
            'gi',
            'gl',
            'gm',
            'gn',
            'gp',
            'gq',
            'gr',
            'gs',
            'gt',
            'gu',
            'gw',
            'gy',
            'hk',
            'hm',
            'hn',
            'hr',
            'ht',
            'hu',
            'id',
            'ie',
            'il',
            'im',
            'in',
            'io',
            'iq',
            'ir',
            'is',
            'it',
            'je',
            'jm',
            'jo',
            'jp',
            'ke',
            'kg',
            'kh',
            'ki',
            'km',
            'kn',
            'kr',
            'kw',
            'ky',
            'kz',
            'la',
            'lb',
            'lc',
            'li',
            'lk',
            'lr',
            'ls',
            'lt',
            'lu',
            'lv',
            'ly',
            'ma',
            'mc',
            'md',
            'mg',
            'mh',
            'mk',
            'ml',
            'mm',
            'mn',
            'mo',
            'mp',
            'mq',
            'mr',
            'ms',
            'mt',
            'mu',
            'mv',
            'mw',
            'mx',
            'my',
            'mz',
            'na',
            'nc',
            'ne',
            'nf',
            'ng',
            'ni',
            'nl',
            'no',
            'np',
            'nr',
            'nu',
            'nz',
            'om',
            'pa',
            'pe',
            'pf',
            'pg',
            'ph',
            'pk',
            'pl',
            'pn',
            'pr',
            'ps',
            'pt',
            'pw',
            'py',
            'qa',
            're',
            'ro',
            'ru',
            'rw',
            'sa',
            'sb',
            'sc',
            'sd',
            'se',
            'sg',
            'sh',
            'si',
            'sk',
            'sl',
            'sm',
            'sn',
            'sr',
            'st',
            'sv',
            'sy',
            'sz',
            'tc',
            'td',
            'tf',
            'tg',
            'th',
            'tj',
            'tk',
            'tl',
            'tm',
            'tn',
            'to',
            'tr',
            'tt',
            'tv',
            'tw',
            'tz',
            'ua',
            'ug',
            'uk',
            'us',
            'uy',
            'uz',
            'va',
            'vc',
            've',
            'vg',
            'vi',
            'vn',
            'vu',
            'wf',
            'ws',
            'ye',
            'yu',
            'za',
            'zm',
            'zw',
            // inactive
            'eh',
            'kp',
            'me',
            'rs',
            'um',
            'bv',
            'gb',
            'pm',
            'sj',
            'so',
            'yt',
            'su',
            'tp',
            'bu',
            'cs',
            'dd',
            'zr'
        ];


        // get domain
        if (!$fullDomain = $this->getUrlDomain($url)) {
            return $baseDomain;
        }

        // break up domain, reverse
        $domain = explode('.', $fullDomain);
        $domain = array_reverse($domain);

        // first check for ip address
        if (count($domain) == 4
            && is_numeric($domain[0])
            && is_numeric($domain[3])
        ) {
            return $fullDomain;
        }

        // if only 2 domain parts, that must be our domain
        if (count($domain) <= 2) {
            return $fullDomain;
        }

        /*
        finally, with 3+ domain parts: obviously D0 is tld
        now, if D0 = ctld and D1 = gtld, we might have something like com.uk
        so, if D0 = ctld && D1 = gtld && D2 != 'www', domain = D2.D1.D0
        else if D0 = ctld && D1 = gtld && D2 == 'www', domain = D1.D0
        else domain = D1.D0
        these rules are simplified below
        */
        if (in_array($domain[0], $cTld)
            && in_array($domain[1], $gTld)
            && $domain[2] != 'www'
        ) {
            $fullDomain = $domain[2] . '.' . $domain[1] . '.' . $domain[0];
        } else {
            $fullDomain = $domain[1] . '.' . $domain[0];;
        }

        return $fullDomain;
    }

    public function getUrlDomain($url)
    {
        $urlParts   = parse_url($url);
        // sanity check
        if (empty($urlParts) || empty($urlParts['host'])) {
            $domain = '';
        } else {
            $domain = strtolower($urlParts['host']);
        }

        return $domain;
    }
}