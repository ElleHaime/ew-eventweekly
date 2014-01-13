<?php
/**
 * Class Session
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Core;


class Session extends \Phalcon\Session\Adapter\Files {

    public function get($index, $defaultValue = null)
    {

        $pieces = explode('.', $index);

        $data = parent::get($pieces[0], $defaultValue);

        if (count($pieces) == 1) {
            return $data;
        }

        if (!empty($data)) {

            unset($pieces[0]);

            if (is_array($data)) {
                switch(count($pieces)) {
                    case 1: $data = $data[1];
                        break;
                    case 2: $data = $data[1][2];
                        break;
                    case 3: $data = $data[1][2][3];
                        break;
                    default:
                        $param = '$data';
                        foreach ($pieces as $node) {
                            $param .= '['.$node.']';
                        }
                        $data = eval($param.';');
                        break;
                }
            }

        }

        return $data;

    }

    public function set($index, $value)
    {
        $pieces = explode('.', $index);

        if (count($pieces) == 1) {
            parent::set($pieces[0], $value);
        }else {
            $key = $pieces[0];

            $data = parent::get($key);

            if (!empty($data)) {

                unset($pieces[0]);

                if (is_array($data)) {
                    switch(count($pieces)) {
                        case 1: $data[$pieces[1]] = $value;
                            break;
                        case 2: $data[$pieces[1]][$pieces[2]] = $value;
                            break;
                        case 3: $data[$pieces[1]][$pieces[2]][$pieces[3]] = $value;
                            break;
                        default:
                            $param = '$data2';
                            foreach ($pieces as $node) {
                                $param .= '[\''.$node.'\']';
                            }
                            $data2 = eval('$data2 = array(); '.$param.' = '.$value.'; return $data2;');
                            $data = array_merge_recursive($data, $data2);
                            break;
                    }
                }

            }

            parent::set($key, $data);

        }
    }

    public function has($index)
    {
        $pieces = explode('.', $index);

        if (count($pieces) == 1) {
            return parent::has($index);
        }else {
            $key = $pieces[0];

            $isset = false;

            $data = parent::get($key);

            if (!empty($data)) {

                unset($pieces[0]);

                if (is_array($data)) {
                    switch(count($pieces)) {
                        case 1: $isset = isset($data[$pieces[1]]);
                            break;
                        case 2: $isset = isset($data[$pieces[1]][$pieces[2]]);
                            break;
                        case 3: $isset = isset($data[$pieces[1]][$pieces[2]][$pieces[3]]);
                            break;
                        default:
                            $param = '$data';
                            foreach ($pieces as $node) {
                                $param .= '[\''.$node.'\']';
                            }
                            $isset = eval('return (isset('.$param.'));');
                            break;
                    }
                }

            }

            return $isset;

        }
    }

} 