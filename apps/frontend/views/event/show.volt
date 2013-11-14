{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container content_noBorder">
    <input type="hidden" id="event_id" value="{{ event['id'] }}">
    <div class="row-fluid ">
    <div class="span12">
    {#<div class="category-title">
        <span>Misic</span>
    </div>#}
    <div class="row-fluid ">
    <div class="span9">
        <div class="list-event clearfix">
            <div class="list-event-img">
                <a href="#"><img src="{{ event['pic_square'] }}"></a>
                {#
                <div class="small-img clearfix">
                    <a href="#myModal" role="button" data-toggle="modal"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>
                    <a  href="#"><img src="/img/demo/small.jpg" alt=""></a>

                    <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <div class="modal-body">
                            <img src="img/demo/list.jpg" alt="">
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary"> more photos &#187; </button>
                #}
            </div>

            <div class="list-event-text">
                <a href="#">{{ event['name'] }}</a>
                <div class="date-list">
                    <i class="icon-time"></i>
                    Start at: <span class="date-start">{{ event['start_time'] }}</span>
                    {#<span class="date-time">20:43</span> <span class="day-title"> - tomorrow</span>#}
                </div>
                <p>{{ event['description']|nl2br }} </p>
                <div class="plans-box clearfix">
                    <span>So, whats your plan?</span>
                    <div class="btn-hide clearfix">
                        <div class="event-site">
                                <div class="event-category categ_green clearfix {% if event['answer'] == 1 %} active-btn {% endif %}">
                                    <span class="color-type green">Im going!</span>
                                    <span class="arrow arrow_green"></span>
                                </div>
                                <div class="event-category categ_yellow clearfix {% if event['answer'] == 2 %} active-btn {% endif %}">
                                    <span class="color-type yellow">Its interesting, maybe im going</span>
                                    <span class="arrow arrow_yellow"></span>
                                </div>
                            {% if !event['answer'] %}
                                <button class="btn" id="event-join">Join</button>
                                <button class="btn" id="event-maybe">Maybe</button>
                                <button class="btn" id="event-decline">Decline</button>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="share-box clearfix">
                    <span class="share-box-title">Share this article:</span>
                    <div class="share-box-icon">
                        <!--Facebook-->
                        <div class="fb-like" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="true"></div>

                        <!--Google +-->
                        <!-- Place this tag where you want the +1 button to render. -->
                        <div class="g-plusone" data-size="medium" data-annotation="none"></div>
                        <!-- Place this tag after the last +1 button tag. -->
                        <script type="text/javascript">
                            (function() {
                                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                po.src = 'https://apis.google.com/js/plusone.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                            })();
                        </script>

                        <!--Twitter-->
                        <a href="https://twitter.com/share" class="twitter-share-button" data-via="Apppicker" data-count="none">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

                        <!--StumbleUpon-->
                        <!-- Place this tag where you want the su badge to render -->
                        <su:badge layout="1"></su:badge>
                        <!-- Place this snippet wherever appropriate -->
                        <script type="text/javascript">
                            (function() {
                                var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
                                li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
                            })();
                        </script>

                        <!--Reddit-->
                        <a href="http://reddit.com/submit" onclick="window.location = 'http://reddit.com/submit?url=' + encodeURIComponent(window.location); return false"> <img src="http://reddit.com/static/spreddit7.gif" alt="submit to reddit" border="0" /> </a>

                        <!--Pinterest-->
                        <a href="//www.pinterest.com/pin/create/button/?url=http%3A%2F%2Fwww.apppicker.com%2Fapplists%2F2732%2FThe-best-sports-trivia-apps-for-iPhone-and-iPad&media=http%3A%2F%2Fwww.apppicker.com%2Fupload%2F2013%2F01%2F22%2Fgzpztqzohy.png&description=The+best+sports+trivia+apps+for+iPhone+and+iPad" data-pin-do="buttonPin" data-pin-config="none"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
                        <script type="text/javascript">
                            (function(d){
                                var f = d.getElementsByTagName('SCRIPT')[0], p = d.createElement('SCRIPT');
                                p.type = 'text/javascript';
                                p.async = true;
                                p.src = '//assets.pinterest.com/js/pinit.js';
                                f.parentNode.insertBefore(p, f);
                            }(document));
                        </script>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="comment-box">
                    <img src="img/demo/comments.jpg" alt="">
                </div>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="sidebar">
            <button type="button" class=" btn btn_invite" id="fb-invite"><img src="img/demo/btn.png" alt="">Invite friends</button>
            {#
            <div class=" place-address">
                Smock Alley Theatre
                <button class="btn btn-primary">
                    <i class="icon-map-marker"></i>
                </button>
            </div>
            #}
            <div class="event-site">
                <p>Event web-site :</p>
                <a target="_blank" href="https://www.facebook.com/events/{{ event['eid'] }}">https://www.facebook.com/events/{{ event['eid'] }}</a>
            </div>
{#
            <div class="event-site">
                <p>Category:</p>
                <div class="event-category clearfix">
                    <span class="color-span orange ">Music</span>
                    <b>/</b>
                    <button class="btn ">Suggest category</button>
                </div>

            </div>
            <div class="event-site">
                <p>Type:</p>
                <div class="event-category clearfix">
                    <span class="color-type gray ">festival</span>
                    <span class="arrow"></span>
                </div>

            </div>
            <div class="event-site">
                <p>Genres:</p>
                <div class="event-category clearfix">
                    <span class="color-type yellow">rock</span>
                    <span class="arrow arrow_yellow"></span>
                </div>
                <div class="event-category clearfix">
                    <span class="color-type yellow">gothic</span>
                    <span class="arrow arrow_yellow"></span>
                </div>

            </div>

            <div class="event-site">
                <p>Venue:</p>
                <div class="event-category clearfix">
                    <span class="color-type light_yellow">gothic</span>
                    <span class="arrow arrow_light-yellow"></span>
                </div>

            </div>
            <div class="event-site tags-box clearfix">
                <p>Tags:</p>
                <div class="event-category clearfix">
                    <span class="color-type blue">Dp</span>
                    <span class="arrow arrow_blue"></span>
                </div>
                <div class="event-category clearfix">
                    <span class="color-type blue">concert</span>
                    <span class="arrow arrow_blue"></span>
                </div>
                <div class="event-category clearfix">
                    <span class="color-type blue">events of the year </span>
                    <span class="arrow arrow_blue"></span>
                </div>

            </div>
#}
        </div>
    </div>
    </div>

    </div>
    </div>


    </div>

{% endblock %}