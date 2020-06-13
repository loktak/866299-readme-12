'use strict';

'use script';

(function () {
  var ESC_KEYCODE = 27;

  window.util = {
    isEscEvent: function (evt, cb) {
      if (evt.keyCode === ESC_KEYCODE) {
        cb();
      }
    },

    getScrollbarWidth: function () {
      return window.innerWidth - document.documentElement.clientWidth;
    }
  }
})();
'use script';

//табы
(function () {
  var switchTabs = function (block) {
    var tabsList = block.querySelector('.tabs__list');
    var tabElements = tabsList.querySelectorAll('.tabs__item');
    var tabContentSections = block.querySelectorAll('.tabs__content');
    var activeTabIndex = 0;
    var initialized = false;

    var initializeSwitch = function () {
      if (!initialized) {
        var detected = false;
        initialized = true;

        for (var i = 0; i < tabElements.length; i++) {
          var tab = tabElements[i];
          if (detected && tab.classList.contains('tabs__item--active')) {
            detected = true;
            activeTabIndex = i;
          }
          addClickHandle(tab, i);
        }
      }
    };

    var addClickHandle = function (tab, index) {
      tab.addEventListener('click', function (evt) {

        goToTab(index);
      });
    };

    var goToTab = function (index) {
      if (index !== activeTabIndex) {
        tabElements[activeTabIndex].classList.remove('tabs__item--active');
        tabElements[index].classList.add('tabs__item--active');
        tabContentSections[activeTabIndex].classList.remove('tabs__content--active');
        tabContentSections[index].classList.add('tabs__content--active');
        if (tabElements[index].classList.contains('filters__button')) {
          var activeFilter;
          activeFilter = tabElements[index].parentNode.parentNode.querySelector('.filters__button--active');
          activeFilter.classList.remove('filters__button--active');
          activeFilter = tabElements[index];
          activeFilter.classList.add('filters__button--active');
        }
        if (tabElements[index].classList.contains('messages__contacts-tab')) {
          var activeContact;
          activeContact = tabElements[index].parentNode.parentNode.querySelector('.messages__contacts-tab--active');
          activeContact.classList.remove('messages__contacts-tab--active');
          activeContact = tabElements[index];
          activeContact.classList.add('messages__contacts-tab--active');
        }
        activeTabIndex = index;
      }
    };

    initializeSwitch();

    return {
      init: initializeSwitch,
      goToTab: goToTab
    };
  }

  var addingPostTabs = document.querySelector('.adding-post__tabs-wrapper');
  var profileTabs = document.querySelector('.profile__tabs-wrapper');
  var messagesTabs = document.querySelector('.messages');

  if (addingPostTabs) {
    var addingPostCollback = switchTabs(addingPostTabs);
  }

  if (profileTabs) {
    var profileCollback = switchTabs(profileTabs);
  }

  if (messagesTabs) {
    var messagesCollback = switchTabs(messagesTabs);
  }
})();
'use script';

(function () {
  var activeModal = document.querySelector('.modal--active');
  var modal = document.querySelector('.modal');
  var modalAdding = document.querySelector('.modal--adding');
  var addingPostSubmit = document.querySelector('.adding-post__submit');
  var scrollbarWidth = window.util.getScrollbarWidth() + 'px';
  var pageMainSection = document.querySelector('.page__main-section');
  var footerWrapper = document.querySelector('.footer__wrapper');

  var showModal = function (openButton, modal) {
    var closeButton = modal.querySelector('.modal__close-button');

    var onPopupEscPress = function (evt) {
      window.util.isEscEvent(evt, closeModal);
    };

    var closeModal = function (evt) {
      modal.classList.remove('modal--active');
      activeModal = false;
      document.removeEventListener('keydown', onPopupEscPress);
      document.documentElement.style.overflowY = 'auto';
      pageMainSection.style.paddingRight = '0';
      footerWrapper.style.paddingRight = '0';
    }

    var openModal = function (evt) {
      if (activeModal) {
        activeModal.classList.remove('modal--active');
      }

      modal.classList.add('modal--active');
      activeModal = modal;
      document.documentElement.style.overflowY = 'hidden';
      pageMainSection.style.paddingRight = scrollbarWidth;
      footerWrapper.style.paddingRight = scrollbarWidth;
      closeButton.focus();

      closeButton.addEventListener('click', function (evt) {

        closeModal();
      });

      modal.addEventListener('click', function (evt) {
        if (evt.target === modal) {
          closeModal();
        }
      })

      document.addEventListener('keydown', onPopupEscPress);
    }

    openButton.addEventListener('click', function (evt) {
      openModal();
    });
  }

  // if (modal) {
  //   showModal(addingPostSubmit, modalAdding);
  // }
})();
'use script';

(function () {
  var sorting = document.querySelector('.sorting');

  if (sorting) {
    var sortingLinks = sorting.querySelectorAll('.sorting__link');
    var sortingLinkActive = sorting.querySelector('.sorting__link--active');

    var onSortingItemClick = function (evt) {
      if (evt.currentTarget === sortingLinkActive) {
        sortingLinkActive.classList.toggle('sorting__link--reverse');
      } else {
        sortingLinkActive.classList.remove('sorting__link--active');
        evt.currentTarget.classList.add('sorting__link--active');
        sortingLinkActive = evt.currentTarget;
      }
    }

    var addSortingListener = function (sortingItem) {
      sortingItem.addEventListener('click', onSortingItemClick);
    }

    for (var i = 0; i < sortingLinks.length; i++) {
      addSortingListener(sortingLinks[i]);
    }
  }
})();
'use script';

(function () {
  var filters = document.querySelector('.filters');

  if (filters) {
    var filtersButtons = filters.querySelectorAll('.filters__button:not(.tabs__item)');
  }

  if (filtersButtons) {
    var filtersButtonActive = filters.querySelector('.filters__button--active');

    var onFiltersButtonClick = function (evt) {
      if (evt.currentTarget !== filtersButtonActive) {
        filtersButtonActive.classList.remove('filters__button--active');
        evt.currentTarget.classList.add('filters__button--active');
        filtersButtonActive = evt.currentTarget;
      }
    }

    var addFiltersListener = function (filtersButton) {
      filtersButton.addEventListener('click', onFiltersButtonClick);
    }

    for (var i = 0; i < filtersButtons.length; i++) {
      addFiltersListener(filtersButtons[i]);
    }
  }
})();
