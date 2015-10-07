<?php

/**
 * Class WPsB_Class_Selector
 * @version 0.1.1
 */
class WPsB_Class_Selector
{
    public function __construct(array $config)
    {
        $defaults_class_config = array(
            "prefix" => "",
            "suffix" => "",
            "store_name" => "my_class"
        );

        $this->class_data = array_merge($defaults_class_config, $config);
    }

    public function register()
    {
        add_action('wp', array($this, 'wp'), 10, 1);

    }

    public function wp(WP $WP)
    {
        if (is_404() && $class_name = $this->get_404_class_name()):
        elseif (is_search() && $class_name = $this->get_search_class_name()):
        elseif (is_front_page() && $class_name = $this->get_front_page_class_name()):
        elseif (is_home() && $class_name = $this->get_home_class_name()):
        elseif (is_post_type_archive() && $class_name = $this->get_post_type_archive_class_name()):
        elseif (is_tax() && $class_name = $this->get_taxonomy_class_name()):
//        elseif (is_attachment() && $class_name = $this->get_attachment_class_name()):
        elseif (is_single() && $class_name = $this->get_single_class_name()):
        elseif (is_category() && $class_name = $this->get_category_class_name()):
        elseif (is_tag() && $class_name = $this->get_tag_class_name()):
        elseif (is_author() && $class_name = $this->get_author_class_name()):
        elseif (is_date() && $class_name = $this->get_date_class_name()):
//        elseif (is_comments_popup() && $class_name = $this->get_comments_popup_class_name()):
//        elseif (is_paged() && $class_name = $this->get_paged_class_name()):
        else:
            $class_name = $this->get_index_class_name();
        endif;

        if ($class_name)
            // must use Autoloader
            set_query_var($this->class_data['store_name'], new $class_name);

        return;
    }

    public function get_404_class_name()
    {
        return $this->get_query_class_name('404');
    }

    public function get_search_class_name()
    {
        return $this->get_query_class_name('search');
    }

    public function get_front_page_class_name()
    {
        $class_names = array(
            'Front_Page',
            'Index'
        );

        return $this->get_query_class_name('front_page', $class_names);
    }

    public function get_home_class_name()
    {
        $class_names = array(
            'Home',
            'Index'
        );

        return $this->get_query_class_name('home', $class_names);
    }

    public function get_post_type_archive_class_name()
    {
        $post_type = ucfirst(get_query_var('post_type'));

        $class_names = array();

        $class_names[] = "Archive_{$post_type}";
        $class_names[] = "Archive";

        return $this->get_query_class_name('archive', $class_names);
    }

    public function get_taxonomy_class_name()
    {
        $term = get_queried_object();

        $class_names = array();

        if (!empty($term->slug)) {
            $taxonomy = $term->texonomy;
            $class_names[] = "Taxonomy_{$taxonomy}_{$term->slug}";
            $class_names[] = "Taxonomy_{$taxonomy}";
        }

        $class_names[] = "Taxonomy";

        return $this->get_query_class_name('taxonomy', $class_names);
    }

    public function get_date_class_name()
    {
        return $this->get_query_class_name('date');
    }

    public function get_single_class_name()
    {
        $post_type = ucfirst(get_post_type());

        $class_names[] = "Single_{$post_type}";
        $class_names[] = "Single";

        return $this->get_query_class_name('single', $class_names);
    }

    public function get_category_class_name()
    {
        $category = get_queried_object();

        $class_names = array();

        if (!empty($category->slug)) {
            $class_names[] = "Category_" . ucfirst($category->slug);
            $class_names[] = "Category";
        }

        return $this->get_query_class_name('category', $class_names);
    }

    public function get_tag_class_name()
    {
        $tag = get_queried_object();

        $class_names = array();

        if (!empty($tag->slug)) {
            $class_names[] = "Tag_" . ucfirst($tag->slug);
        }
        $class_names[] = 'Tagp';

        return $this->get_query_class_name('tag', $class_names);
    }

    public function get_author_class_name()
    {
        $author = get_queried_object();

        $class_names = array();

        if ($author instanceof WP_User) {
            $class_names[] = "Author_" . ucfirst($author->user_nicename);
        }
        $class_names[] = "Author";

        return $this->get_query_class_name('author', $class_names);
    }

    public function get_index_class_name()
    {
        return $this->get_query_class_name('index');
    }

    public function add_affix($context)
    {
        return $this->get_class_data('prefix') . "_" . $context . "_" . $this->get_class_data('suffix');
    }

    public function get_class_data($key)
    {
        if (isset($this->class_data[$key])) {
            return $this->class_data[$key];
        }

        return false;
    }

    public function get_query_class_name($type, array $class_names = array())
    {
        if (empty($class_names)) {
            $class_name = ucfirst($type);
            $class_names = array("{$class_name}");
        }

        $class_name = apply_filters("{$type}_theme_class_name", $this->locate_class_name($class_names));

        return $this->add_affix($class_name);
    }

    public function locate_class_name(array $class_names)
    {
        // with autoloader
        foreach ((array)$class_names as $class_name) {
            if (!$class_name) continue;
            if (class_exists($class_name)) {
                return $class_name;
            }

            $located = $class_name;
        }


        return $located;
    }
}
