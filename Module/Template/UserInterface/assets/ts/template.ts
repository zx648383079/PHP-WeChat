const DEL_URI = 'weight/destroy',
    NEW_URI = 'weight/create',
    REFRESH_URI = 'weight/refresh',
    EDIT_URI = 'weight/save',
    SETTING_URI = 'weight/setting',
    SAVE_SETTING_URI = 'weight/save_setting',
    INFO_URI = 'weight/info';

class Panel {
    constructor(
        tag: string,
        public page: Page
    ) {
        this.element = $(tag);
        this._init();
    }

    public element: JQuery;

    private _init() {
        this._bindEvent();
    }

    private _bindEvent() {
        let that = this;
        this.element.find(".panel .head .fa-close").click(function() {
            that.element.addClass("min");
        });
        this.element.find(".menu>li>.head").click(function() {
            $(this).parent().toggleClass("active");
        });
    }

    public toggle(state?: boolean) {
        if (typeof state == 'boolean') {
            state = !state;
        }
        this.element.toggleClass('min', state);
        return this;
    }

    public bindDrag() {
        let that = this;
        this.element.find(".weight-edit-grid").draggable({
            connectToSortable: ".weight-row",
            helper: "clone",
            opacity: .3,
            revert: "invalid",
            start: function() {
                that.page.body.addClass("hover");
            },
            stop: function(event, target) {
                that.page.body.removeClass("hover");
                console.log(arguments);
                
                let ele = target.helper,
                    row = ele.closest('.weight-row');
                if (!row || row.length < 1) {
                    if (ele.hasClass('weight-row')) {
                        row = ele;
                    } else {
                        return;
                    }
                }
                that.page.addWeight(ele, row);
            }
        });
    }
}

class Weight {
    constructor(
        public box: JQuery
    ) {
        
    }

    public toggle(state?: boolean) {
        this.box.toggleClass('weight-edit-mode', state);
        return this;
    }

    public toggleLoading(state?: boolean) {
        this.box.toggleClass('weight-loading', state);
        return this;
    }

    /**
     * html
     */
    public html(html?: string) {
        return this.box.replaceWith(html);
    }

    /**
     * id
     */
    public id(): number {
        return parseInt(this.box.attr('data-id'));
    }

    /**
     * weightId
     */
    public weightId(): number {
        return parseInt(this.box.attr('data-weight'));
    }
}

class Page {
    constructor(
        public id: number,
        public baseUri: string
    ) {
        this.box = $("#page-box");
        this.element = $('#mainMobile');
        this.bodyBox = $('#mainGrid');
        this.body = this.bodyBox.contents().find('body') as JQuery;
        
        this.weightBox = new Panel('#weight', this),
        this.propertyBox = new Panel('#property', this);
        this._init();
    }

    public box: JQuery;

    public element: JQuery;

    public bodyBox: JQuery;

    public body: JQuery;

    public weightBox: Panel;

    public propertyBox: Panel;

    public weight: Weight;

    private _cacheSettings: any;

    private _init() {
        this._bindEvent();
        this.resize();
    }

    private _bindEvent() {
        let that = this;
        $(window).resize(function() {
            that.resize();
        });
        this.body.find(".weight-row").sortable({
            connectWith: ".weight-row"
        });
        this.body.on("click", ".weight-action .del", function() {
            that.removeWeight($(this).closest('.weight-edit-grid'));
        }).on("click", ".weight-action .edit", function(e) {
            e.stopPropagation();
            that.setWeight($(this).parents('.weight-edit-grid'));
        }).on("click", ".weight-action .refresh", function(e) {
            e.stopPropagation();
            that.refreshWeight($(this).closest('.weight-edit-grid'));
        });
        this.weightBox.bindDrag();
        this.bindRule();
    }

    /**
     * bindRule
     */
    public bindRule() {
        let line: JQuery,
            mode: number = 0;
        let ruleBox = this.element.find('.rule-box').on('mousedown', '.rule-lines div', function(e) {
            e.stopPropagation();
            mode = 0;
            line = $(this);
        }).on('mousedown', '.top-rule', function() {
            mode = 1;
        }).on('mousemove', '.top-rule', function() {
            if (mode === 1) {
                line = $('<div class="h-line"></div>');
                ruleBox.find('.rule-lines').append(line);
                mode = 0;
            }
        }).on('mousedown', '.left-rule', function() {
            mode = 2;
        }).on('mousemove', '.left-rule', function() {
            if (mode === 2) {
                line = $('<div class="v-line"></div>');
                ruleBox.find('.rule-lines').append(line);
                mode = 0;
            }
        });
        $(document).on('mousemove', function(e: any) {
            if (!line) {
                return;
            }
            if (line.hasClass('v-line')) {
                line.css('left', e.clientX + 'px');
                return;
            }
            let top = e.clientY - ruleBox.offset().top;
            line.css('top', top + 'px');
        }).on('mouseup', function() {
            if (!line) {
                return;
            }
            if (line.hasClass('v-line')) {
                line.offset().left < 20 && line.remove();
            } else {
                (line.offset().top - ruleBox.offset().top) < 20 && line.remove();
            }
            line = undefined;
            
            
        });
    }

    /**
     * refreshWeight
     */
    public refreshWeight(element: JQuery) {
        let id = element.attr('data-id'),
            weight = this.setWeight(element, false).toggleLoading(true);
        this.post(REFRESH_URI, {
            id: id
        }, function(data) {
            weight.toggleLoading(false);
            if (data.code == 200) {
                weight.html(data.data.html);
            }
        });
    }

    public setWeight(element: JQuery, withEdit: boolean = true): Weight {
        if (this.weight) {
            this.weight.toggle(false);
        }
        this.weight = new Weight(element);
        this.weight.toggle(true);
        if (withEdit) {
            this.weightBox.toggle(true);
            this.propertyBox.toggle(true);
        }
        return this.weight;
    }

    public removeWeight(element: JQuery) {
        this.post(DEL_URI, {
            id: element.attr('data-id')
        }, function(data) {
            if (data.code == 200) {
                element.remove();
            }
        });
    }

    public addWeight(element: JQuery, parent: JQuery): Weight {
        let that = this;
        element.width('auto');
        let weight = this.setWeight(element).toggleLoading(true);
        this.post(NEW_URI, {
            weight_id: element.attr('data-weight'),
            parent_id: parent.attr('data-id')
        }, function(data) {
            weight.toggleLoading(false);
            if (data.code == 200) {
                weight.html(data.data.html);
            }
        });
        return weight;
    }

    public resize() {
        this.box.height($(window).height() - 57);
        this.drawRule(this.element.find('.top-rule'), 20);
        
        this.drawRule(this.element.find('.left-rule'), 20);
        this.bodyBox.width(this.element.width() - 10);
        this.bodyBox.height(this.element.height());
    }

    public html(): string {
        return $.htmlClean(this.body.html(), {
            format: true,
            allowedAttributes: [
                ["id"],
                ["class"],
                ["data-toggle"],
                ["data-target"],
                ["data-parent"],
                ["role"],
                ["data-dismiss"],
                ["aria-labelledby"],
                ["aria-hidden"],
                ["data-slide-to"],
                ["data-slide"]
            ]
        });
    }

    /**
     * getSetting
     */
    public getSetting(id: number, cb: (data: any) => void) {
        let that = this;
        if (this._cacheSettings.hasOwnProperty(id)) {
            cb && cb(this._cacheSettings[id]);
            return this;
        }
        this.post(SETTING_URI, {
            id: id
        }, function(data) {
            if (data.code == 200) {
                that._cacheSettings[id] = data.data;
                cb && cb(data.data);
            }
        });
        return this;
    }

    /**
     * saveSetting
     */
    public saveSetting(id: number, args: any, cb: (data: any) => void) {
        let that = this;
        this.post(SAVE_SETTING_URI, {
            id: id,
            setting: args
        }, function(data) {
            if (data.code == 200) {
                that._cacheSettings[id] = data;
                cb && cb(data.data);
            }
        });
        return this;
    }

    /**
     * postJson
     */
    public post(path: string, data: any, cb: any) {
        data['page_id'] = this.id;
        postJson(this.baseUri + path, data, cb);
        return this;
    }

    /**
     * drawRule
     */
    public drawRule(box: JQuery, start: number, scale: number = 1) {
        let width = box.width(), height = box.height();
        let canvas: HTMLCanvasElement = box[0] as HTMLCanvasElement;
        canvas.width = width;
        canvas.height = height;
        let context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);
        let direct = canvas.width > canvas.height; // true 横向
        let length = direct ? canvas.width : canvas.height;
        
        for (let i = 0; i < length; i+= 10) {
            let real = i - start;
            let len = real % 50 === 0 ? 10 : 5; 
            this.drawLine(context, direct,  i, len, len > 5 ? real.toString() : undefined);
        }
    }

    private drawLine(context: CanvasRenderingContext2D, direct: boolean, i: number, length: number, tip?: string) {
        if (direct) {
            context.moveTo(i, 20 - length);
            context.lineTo(i, 20);
        } else {
            context.moveTo(20 - length, i);
            context.lineTo(20, i);
        }
        context.lineWidth = 1;
        context.strokeStyle = "red";
        context.stroke();
        if (!tip) {
            return;
        }
        context.font = '6px Microsoft YaHei';
        if (direct) {
            context.fillText(tip, i- 5, 10);
        } else {
            context.fillText(tip, 0, i + 3);
        }
        
    }
}


function bindPage(pageId: number, baseUri: string) {
    let page = new Page(pageId, baseUri);
    $(".mobile-size li").click(function() {
        $(".mobile-size").parent().removeClass("open");
        let size = $(this).attr("data-size").split("*");
        page.element.removeClass().addClass('mobile-' + size[0]);
    });
    $(".navbar>li>div").click(function() {
        $(this).parent().toggleClass("open");
    });
    $(".mobile-size li").click(function() {
        $(this).addClass("active").siblings().removeClass("active");
    });

    $(".mobile-rotate").click(function() {
        page.element.toggleClass('rotate');
    });
    $(".expand>.head").click(function() {
        $(this).parent().toggleClass("open");
    });
}